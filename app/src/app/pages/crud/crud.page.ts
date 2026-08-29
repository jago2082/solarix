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
  ) {}

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
    return this.config?.fields.filter(f => f.key !== 'id') || [];
  }

  get globalFilterFields(): string[] {
    return this.tableFields.map(f => f.key);
  }

  private buildForm() {
    if (!this.config) return;
    const group: Record<string, any> = {};
    for (const field of this.config.fields) {
      if (field.key === 'id') continue;
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
            const list = Array.isArray(res) ? res : [];
            this.lookups[field.key] = {};
            for (const item of list) {
              const key = item[field.source!.valueField];
              this.lookups[field.key][key] = item;
            }
            resolve();
          },
          error: () => {
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
        this.items = Array.isArray(res) ? res : [];
      },
      error: (err) => {
        this.loading = false;
        console.error(err);
        this._alert.error('Error al consultar registros');
      }
    });
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
        return related[field.source.labelField] || value;
      }
      return value;
    }
    if (field.options) {
      const option = field.options.find(o => o.value === value);
      return option ? option.label : value;
    }
    return value;
  }

  abrirModal(item?: any) {
    this.editing = !!item;
    this.selectedId = item?.id;
    this.isModalOpen = true;

    if (item) {
      const patch: Record<string, any> = {};
      for (const field of this.formFields) {
        patch[field.key] = item[field.key];
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
