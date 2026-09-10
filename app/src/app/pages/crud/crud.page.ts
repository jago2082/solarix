import { Component, Input, inject, OnInit, OnChanges, SimpleChanges, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, NavController, LoadingController, AlertController } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators, ValidatorFn } from '@angular/forms';
import { ApiService } from 'src/app/services/api/api.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { EntityConfigService, EntityConfig, CrudField } from 'src/app/services/entity-config/entity-config.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { Table } from 'primeng/table';

// Importar addIcons y los íconos específicos de Ionicons
import { addIcons } from 'ionicons';
import { create, trash, createOutline, trashOutline, addOutline, closeOutline, eyeOutline } from 'ionicons/icons';

@Component({
  selector: 'app-crud-generic',
  templateUrl: './crud.page.html',
  styleUrls: ['./crud.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    IonicModule,
    FormsModule,
    ReactiveFormsModule,
    HeaderComponent,
    FooterComponent,
    TableModule,
    InputTextModule
  ]
})
export class CrudGenericComponent implements OnInit, OnChanges {
  private fb = inject(FormBuilder);

  @Input() entityKey!: string;

  config?: EntityConfig;
  form: FormGroup = this.fb.group({});
  loading = false;
  saving = false;

  @ViewChild('table') table!: Table;
  globalFilter: string = '';
  items: any[] = [];
  isModalOpen = false;
  editing = false;
  selectedId?: string | number;
  lookups: Record<string, Record<string | number, any>> = {};

  constructor(
    private _apiService: ApiService,
    private _alert: AlertService,
    private _nav: NavController,
    private _loadingCtrl: LoadingController,
    private _alertCtrl: AlertController,
    private _sesion: SessionsService,
    private _entityConfig: EntityConfigService
  ) {
    // REGISTRO DE ÍCONOS DE IONICONS
    addIcons({
      create,
      trash,
      'create-outline': createOutline,
      'trash-outline': trashOutline,
      'add-outline': addOutline,
      'close-outline': closeOutline,
      'eye-outline': eyeOutline
    });
  }

  async ngOnInit() {
    if (!this._sesion.IsAdmin()) {
      this._alert.error('No tiene permisos para acceder a esta opción');
      this._nav.navigateRoot('tabs/in', { animated: true });
      return;
    }
    await this.initEntity();
  }

  async ngOnChanges(changes: SimpleChanges) {
    if (changes['entityKey'] && !changes['entityKey'].firstChange) {
      await this.initEntity();
    }
  }

  private async initEntity() {
    const cfg = this._entityConfig.get(this.entityKey);
    if (!cfg) {
      this._alert.error('Entidad no configurada');
      this._nav.navigateRoot('tabs/admin', { animated: true });
      return;
    }
    this.config = cfg;
    this.buildForm();
    await this.loadLookups();
    this.loadItems();
  }

  get tableFields(): CrudField[] {
    return this.config?.fields.filter(f => f.showInTable) || [];
  }

  get formFields(): CrudField[] {
    return this.config?.fields.filter(f => f.key !== 'id' && !f.computed) || [];
  }

  get computedFields(): CrudField[] {
    return this.config?.fields.filter(f => f.computed) || [];
  }

  /** Indica si la entidad actual soporta vista de detalle de proyección */
  get hasDetailView(): boolean {
    return this.entityKey === 'variables-plantilla';
  }

  get globalFilterFields(): string[] {
    return this.tableFields.map(f => f.key);
  }

  private buildForm() {
    if (!this.config) return;
    const group: Record<string, any> = {};
    for (const field of this.config.fields) {
      if (field.key === 'id' || field.computed) continue;
      const validators: ValidatorFn[] = [];
      if (field.required) validators.push(Validators.required);
      group[field.key] = ['', validators];
    }
    this.form = this.fb.group(group);
  }

  private async loadLookups() {
    if (!this.config) return;
    const sourceFields = this.config.fields.filter(f => f.source);
    const requests = sourceFields.map(field =>
      new Promise<void>((resolve) => {
        this._apiService.crudGetAll(field.source!.endpoint).subscribe({
          next: (res: any) => {
            let listData: any[] = [];
            if (Array.isArray(res)) {
              listData = res;
            } else if (res && typeof res === 'object') {
              listData = res.data || res.datos || res.result || res.usuarios || res.roles || Object.values(res).find(val => Array.isArray(val)) || [];
            }

            this.lookups[field.key] = {};
            for (const item of listData) {
              const normalizedItem = this.normalizeItem(item);
              const key = normalizedItem[field.source!.valueField] ?? item[field.source!.valueField];
              if (key !== undefined && key !== null) {
                this.lookups[field.key][key] = normalizedItem;
              }
            }
            resolve();
          },
          error: (err) => {
            console.warn(`Error al cargar datos dinámicos para ${field.key}:`, err);
            this.lookups[field.key] = {};
            resolve();
          }
        });
      })
    );
    await Promise.all(requests);
  }

  loadItems() {
    if (!this.config) return;
    this.loading = true;
    this._apiService.crudGetAll(this.config.endpoint).subscribe({
      next: (res: any) => {
        this.loading = false;

        let rawList: any[] = [];
        if (Array.isArray(res)) {
          rawList = res;
        } else if (res && typeof res === 'object') {
          rawList = res.data || res.datos || res.result || res.roles || res.usuarios || Object.values(res).find(val => Array.isArray(val)) || [];
        }

        this.items = rawList.map(item => this.normalizeItem(item));
      },
      error: (err) => {
        this.loading = false;
        console.error('Error en loadItems:', err);
        this._alert.error('Error al consultar registros');
      }
    });
  }

  /**
   * Homologa las respuestas entregadas por los DAO en PHP Slim
   */
  private normalizeItem(item: any): any {
    if (!item || typeof item !== 'object') return item;

    const normalized: Record<string, any> = { ...item };

    // Claves primarias
    normalized['id'] = item.id ?? item.lInUro_cont ?? item.rol_id ?? item.rolId ?? item.codigo ?? item.id_rol;
    
    // Relaciones
    normalized['usuarioId'] = item.usuarioId ?? item.lInUsu_cont ?? item.usuario_id ?? item.id_usuario;
    normalized['rolId'] = item.rolId ?? item.lInRol_cont ?? item.rol_id ?? item.id_rol;
    normalized['clienteId'] = item.clienteId ?? item.cliente_id ?? item.id_cliente;
    normalized['sedeId'] = item.sedeId ?? item.sede_id ?? item.id_sede;

    // Atributos de texto
    normalized['nombre'] = item.nombre ?? item.rol_nombre ?? item.rolNombre ?? item.nombres ?? item.nombreCompleto ?? item.email;
    normalized['descripcion'] = item.descripcion ?? item.rol_descripcion ?? item.rolDescripcion ?? '';
    normalized['estado'] = item.estado ?? item.rol_estado ?? item.rolEstado ?? 'A';
    normalized['fecha'] = item.fecha ?? item.fechaVisita ?? item.created_at;

    return normalized;
  }

  aplicarFiltroGlobal(event: Event) {
    const input = event.target as HTMLInputElement;
    this.table?.filterGlobal(input.value, 'contains');
  }

  displayValue(item: any, field: CrudField): string {
    const value = item[field.key];
    if (field.source && this.lookups[field.key]) {
      const related = this.lookups[field.key][value];
      if (related) {
        return related[field.source.labelField] || related['nombre'] || value;
      }
      return value;
    }
    if (field.options) {
      const option = field.options.find(o => o.value === value || o.value === String(value));
      return option ? option.label : value;
    }
    return value !== undefined && value !== null ? String(value) : '';
  }

  abrirModal(item?: any) {
    this.editing = !!item;
    this.selectedId = item?.id;
    this.isModalOpen = true;

    if (item) {
      const patch: Record<string, any> = {};
      for (const field of this.formFields) {
        let val = item[field.key] ?? '';

        // Formateo de cadena de fecha/hora para compatibilidad con <ion-input type="datetime-local">
        if (field.type === 'datetime-local' && val && typeof val === 'string') {
          val = val.replace(' ', 'T').substring(0, 16);
        } else if (field.type === 'date' && val && typeof val === 'string') {
          val = val.substring(0, 10);
        }

        patch[field.key] = val;
      }
      this.form.patchValue(patch);
    } else {
      this.form.reset();
    }
    this.form.markAsPristine();
    this.form.markAsUntouched();
  }

  cerrarModal() {
    this.isModalOpen = false;
    this.editing = false;
    this.selectedId = undefined;
    this.form.reset();
  }

  private parseValue(field: CrudField, value: any): any {
    if (field.type === 'number') {
      return value === '' || value == null ? null : Number(value);
    }
    return value;
  }

  async guardar() {
    if (!this.config) return;
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const raw = this.form.getRawValue();
    const body: Record<string, any> = {};
    for (const field of this.formFields) {
      body[field.key] = this.parseValue(field, raw[field.key]);
    }

    // Calcular campos automáticos (ej. variables de proyección)
    if (this.entityKey === 'variables-plantilla') {
      this.calcularCamposProyeccion(body);
    }

    const loading = await this._loadingCtrl.create({
      message: this.editing ? 'Actualizando...' : 'Registrando...'
    });
    await loading.present();
    this.saving = true;

    const operation = this.editing && this.selectedId
      ? this._apiService.crudUpdate(this.config.endpoint, this.selectedId, body)
      : this._apiService.crudCreate(this.config.endpoint, body);

    operation.subscribe({
      next: (resp: any) => {
        loading.dismiss();
        this.saving = false;
        this._alert.success(resp?.message || 'Operación exitosa');
        this.cerrarModal();
        this.loadItems();
      },
      error: (err) => {
        loading.dismiss();
        this.saving = false;
        console.error(err);
        this._alert.error('Error en la operación');
      }
    });
  }

  /**
   * Calcula los campos derivados de una variable de proyección.
   * Unidades: tarifas en $/kWh, consumo/generación en GWh, costos en millones de $.
   * ($/kWh × GWh = millones de $)
   */
  private calcularCamposProyeccion(body: Record<string, any>) {
    const tarifaConvencional = Number(body['tarifaConvencional']) || 0;
    const consumo = Number(body['consumoEnergia']) || 0;
    const generacion = Number(body['generacionEnergia']) || 0;
    const costoRed = Number(body['costoRedRemanente']) || 0;

    // Descuento del plan PPA (porcentaje) obtenido del lookup cargado
    const plan = this.lookups['planPpaId']?.[body['planPpaId']];
    const descuento = plan ? (Number(plan['descuento']) || 0) / 100 : 0;

    const tarifaPpa = tarifaConvencional * (1 - descuento);
    const costoConsumoSinSsfv = tarifaConvencional * consumo;
    const costoSsfvPpa = tarifaPpa * generacion;
    const costoSsfvCostoRed = costoSsfvPpa + costoRed;

    body['tarifaPpa'] = tarifaPpa;
    body['costoConsumoSinSsfv'] = costoConsumoSinSsfv;
    body['costoSsfvPpa'] = costoSsfvPpa;
    body['costoSsfvCostoRed'] = costoSsfvCostoRed;
    body['ahorroMillones'] = costoConsumoSinSsfv - costoSsfvCostoRed;
  }

  /** Navega a la página de detalle de proyección del plan PPA */
  verDetalle(item: any) {
    const planPpaId = item?.planPpaId;
    if (planPpaId) {
      this._nav.navigateForward(`tabs/admin/variables-plantilla/detalle/${planPpaId}`, { animated: true });
    }
  }

  async eliminar(item: any) {
    const alert = await this._alertCtrl.create({
      header: 'Confirmar eliminación',
      message: `¿Desea eliminar el registro?`,
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: () => this.confirmarEliminar(item)
        }
      ]
    });
    await alert.present();
  }

  confirmarEliminar(item: any) {
    if (!this.config) return;
    this._apiService.crudDelete(this.config.endpoint, item.id).subscribe({
      next: (resp: any) => {
        this._alert.success(resp?.message || 'Registro eliminado');
        this.loadItems();
      },
      error: (err) => {
        console.error(err);
        this._alert.error('Error al eliminar registro');
      }
    });
  }

  trackByField(index: number, field: CrudField) {
    return field.key;
  }
}