import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, NavController } from '@ionic/angular';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from 'src/app/services/api/api.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';

export interface FilaProyeccion {
  ano: number;
  tarifaConvencional: number;
  tarifaPpa: number;
  consumoEnergia: number;
  generacionEnergia: number;
  costoConsumoSinSsfv: number;
  costoRedRemanente: number;
  costoSsfvPpa: number;
  costoSsfvCostoRed: number;
  ahorroMillones: number;
}

@Component({
  selector: 'app-proyeccion-detalle',
  templateUrl: './proyeccion-detalle.page.html',
  styleUrls: ['./proyeccion-detalle.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    IonicModule,
    FormsModule,
    HeaderComponent,
    FooterComponent
  ]
})
export class ProyeccionDetallePage implements OnInit {
  private route = inject(ActivatedRoute);

  planes: any[] = [];
  planPpaId: number | null = null;
  plan: any = null;

  // Valores digitables (primera fila)
  tarifaConvencional: number | null = null;
  consumoEnergia: number | null = null;
  generacionEnergia: number | null = null;
  costoRedRemanente: number | null = 0;

  // Parámetros de indexación anual (%) - ajustables según el estudio
  indexacionConvencional = 7;    // Incremento anual tarifa convencional
  indexacionPpa = 3;             // Incremento anual tarifa PPA
  degradacionAnual = 0.4;        // Degradación anual de generación SSFV
  factorCo2 = 0.493;             // tCO2 por MWh
  arbolesPorTonelada = 7;        // Árboles equivalentes por tCO2

  filas: FilaProyeccion[] = [];
  calculado = false;
  guardando = false;
  maestroId: number | null = null;

  constructor(
    private _apiService: ApiService,
    private _alert: AlertService,
    private _nav: NavController,
    private _sesion: SessionsService
  ) {}

  async ngOnInit() {
    if (!this._sesion.IsAdmin()) {
      this._alert.error('No tiene permisos para acceder a esta opción');
      this._nav.navigateRoot('tabs/in', { animated: true });
      return;
    }

    const param = this.route.snapshot.paramMap.get('planPpaId');
    this.planPpaId = param ? Number(param) : null;

    this.cargarPlanes();
  }

  cargarPlanes() {
    this._apiService.crudGetAll('planes-ppa').subscribe({
      next: (res: any) => {
        let listData: any[] = [];
        if (Array.isArray(res)) {
          listData = res;
        } else if (res && typeof res === 'object') {
          listData = res.data || res.datos || res.result || Object.values(res).find(v => Array.isArray(v)) || [];
        }
        this.planes = listData;

        if (this.planPpaId) {
          this.onPlanChange();
        }
      },
      error: (err) => {
        console.error('Error cargando planes PPA:', err);
        this._alert.error('Error al consultar los planes PPA');
      }
    });
  }

  onPlanChange() {
    this.plan = this.planes.find(p => Number(p.id) === Number(this.planPpaId)) || null;
    this.filas = [];
    this.calculado = false;

    if (this.plan) {
      this.prefillDesdeRegistros();
    }
  }

  /**
   * Precarga los valores del maestro (año 1) y sus detalles si ya existen
   * registros de variables-plantilla para el plan seleccionado.
   */
  private prefillDesdeRegistros() {
    this._apiService.crudGetAll('variables-plantilla').subscribe({
      next: (res: any) => {
        let listData: any[] = [];
        if (Array.isArray(res)) {
          listData = res;
        } else if (res && typeof res === 'object') {
          listData = res.data || res.datos || res.result || Object.values(res).find(v => Array.isArray(v)) || [];
        }

        const registrosPlan = listData.filter(r => Number(r.planPpaId) === Number(this.planPpaId));
        const ano1 = registrosPlan.find(r => Number(r.anoProyeccion) === 1) || registrosPlan[0];

        if (ano1) {
          this.maestroId = ano1.id ?? null;
          this.tarifaConvencional = Number(ano1.tarifaConvencional) || null;
          this.consumoEnergia = Number(ano1.consumoEnergia) || null;
          this.generacionEnergia = Number(ano1.generacionEnergia) || null;
          this.costoRedRemanente = Number(ano1.costoRedRemanente) || 0;
          this.cargarDetallesExistentes(this.maestroId!);
        } else {
          this.maestroId = null;
        }
      },
      error: (err) => console.warn('No se pudieron precargar variables:', err)
    });
  }

  private cargarDetallesExistentes(vplCont: number) {
    if (!vplCont) return;
    this._apiService.crudGetAll(`dtlle-Variables-plantilla/por-vpl/${vplCont}`).subscribe({
      next: (res: any) => {
        let listData: any[] = [];
        if (Array.isArray(res)) {
          listData = res;
        } else if (res && typeof res === 'object') {
          listData = res.data || res.datos || res.result || Object.values(res).find(v => Array.isArray(v)) || [];
        }

        if (listData.length > 0) {
          this.filas = listData.map(d => ({
            ano: Number(d.anoProyeccion),
            tarifaConvencional: Number(d.tarifaConvencional),
            tarifaPpa: Number(d.tarifaPpa),
            consumoEnergia: Number(d.consumoEnergia),
            generacionEnergia: Number(d.generacionEnergia),
            costoConsumoSinSsfv: Number(d.costoConsumoSinSsfv),
            costoRedRemanente: Number(d.costoRedRemanente),
            costoSsfvPpa: Number(d.costoSsfvPpa),
            costoSsfvCostoRed: Number(d.costoSsfvCostoRed),
            ahorroMillones: Number(d.ahorroMillones)
          })).sort((a, b) => a.ano - b.ano);
          this.calculado = true;
        }
      },
      error: (err) => console.warn('No se pudieron cargar los detalles:', err)
    });
  }

  get duracionAnos(): number {
    return this.plan ? Number(this.plan.duracionAnos) || 20 : 20;
  }

  get descuento(): number {
    return this.plan ? (Number(this.plan.descuento) || 0) / 100 : 0;
  }

  get inputsValidos(): boolean {
    return !!this.plan
      && this.tarifaConvencional != null && this.tarifaConvencional > 0
      && this.consumoEnergia != null && this.consumoEnergia > 0
      && this.generacionEnergia != null && this.generacionEnergia > 0
      && this.costoRedRemanente != null && this.costoRedRemanente >= 0;
  }

  /**
   * Genera la tabla de proyección año a año.
   * Unidades: tarifas $/kWh, energía GWh, costos en millones de $.
   * ($/kWh × GWh = millones de $)
   */
  calcular() {
    if (!this.inputsValidos) {
      this._alert.error('Complete los valores de la primera fila y seleccione un plan PPA');
      return;
    }

    const anos = this.duracionAnos;
    const escConv = 1 + this.indexacionConvencional / 100;
    const escPpa = 1 + this.indexacionPpa / 100;
    const deg = 1 - this.degradacionAnual / 100;

    const tarifaPpaBase = this.tarifaConvencional! * (1 - this.descuento);

    this.filas = [];
    for (let n = 1; n <= anos; n++) {
      const tarifaConvencional = this.tarifaConvencional! * Math.pow(escConv, n - 1);
      const tarifaPpa = tarifaPpaBase * Math.pow(escPpa, n - 1);
      const consumo = this.consumoEnergia!;
      const generacion = this.generacionEnergia! * Math.pow(deg, n - 1);
      const costoRed = this.costoRedRemanente!;

      const costoConsumoSinSsfv = tarifaConvencional * consumo;
      const costoSsfvPpa = tarifaPpa * generacion;
      const costoSsfvCostoRed = costoSsfvPpa + costoRed;
      const ahorro = costoConsumoSinSsfv - costoSsfvCostoRed;

      this.filas.push({
        ano: n,
        tarifaConvencional,
        tarifaPpa,
        consumoEnergia: consumo,
        generacionEnergia: generacion,
        costoConsumoSinSsfv,
        costoRedRemanente: costoRed,
        costoSsfvPpa,
        costoSsfvCostoRed,
        ahorroMillones: ahorro
      });
    }

    this.calculado = true;
  }

  // ---- Totales ----
  get totalConsumo(): number {
    return this.filas.reduce((s, f) => s + f.consumoEnergia, 0);
  }

  get totalGeneracion(): number {
    return this.filas.reduce((s, f) => s + f.generacionEnergia, 0);
  }

  get totalCostoSsfvRed(): number {
    return this.filas.reduce((s, f) => s + f.costoSsfvCostoRed, 0);
  }

  get totalAhorro(): number {
    return this.filas.reduce((s, f) => s + f.ahorroMillones, 0);
  }

  /** Ahorro agrupado por bloques de 5 años */
  get ahorroPorBloques(): { rango: string; valor: number }[] {
    const bloques: { rango: string; valor: number }[] = [];
    for (let i = 0; i < this.filas.length; i += 5) {
      const grupo = this.filas.slice(i, i + 5);
      const valor = grupo.reduce((s, f) => s + f.ahorroMillones, 0);
      bloques.push({ rango: `Año ${grupo[0].ano}-${grupo[grupo.length - 1].ano}`, valor });
    }
    return bloques;
  }

  /** tCO2 evitadas: generación total (GWh → MWh) × factor */
  get totalCo2(): number {
    return this.totalGeneracion * 1000 * this.factorCo2;
  }

  get totalArboles(): number {
    return this.totalCo2 * this.arbolesPorTonelada;
  }

  /**
   * Guarda el encabezado (año 1) y los detalles calculados en el maestro-detalle.
   */
  guardarProyeccion() {
    if (!this.inputsValidos || !this.filas.length) {
      this._alert.error('Calcule la proyección antes de guardar');
      return;
    }
    if (this.guardando) return;
    this.guardando = true;

    const maestro = {
      planPpaId: this.planPpaId,
      anoProyeccion: 1,
      tarifaConvencional: this.tarifaConvencional,
      consumoEnergia: this.consumoEnergia,
      generacionEnergia: this.generacionEnergia,
      costoRedRemanente: this.costoRedRemanente
    };

    if (this.maestroId) {
      this._apiService.crudUpdate('variables-plantilla', this.maestroId!, maestro).subscribe({
        next: () => this.guardarDetalles(this.maestroId!),
        error: (err) => {
          this.guardando = false;
          console.error(err);
          this._alert.error(this.extractError(err, 'Error actualizando el encabezado'));
        }
      });
    } else {
      this._apiService.crudCreate('variables-plantilla', maestro).subscribe({
        next: (resp: any) => {
          const id = resp?.id ?? resp?.data?.id;
          if (!id) {
            this.guardando = false;
            this._alert.error('No se pudo obtener el ID del encabezado');
            return;
          }
          this.maestroId = id;
          this.guardarDetalles(id);
        },
        error: (err) => {
          this.guardando = false;
          console.error(err);
          this._alert.error(this.extractError(err, 'Error creando el encabezado'));
        }
      });
    }
  }

  private guardarDetalles(vplCont: number) {
    this._apiService.crudDelete('dtlle-Variables-plantilla/por-vpl', vplCont).subscribe({
      next: () => this.crearDetalles(vplCont),
      error: (err) => {
        this.guardando = false;
        console.error(err);
        this._alert.error(this.extractError(err, 'Error limpiando detalles previos'));
      }
    });
  }

  private crearDetalles(vplCont: number) {
    let completados = 0;
    const total = this.filas.length;
    let huboError = false;

    for (const fila of this.filas) {
      const detalle = {
        vplCont,
        anoProyeccion: fila.ano,
        tarifaConvencional: fila.tarifaConvencional,
        tarifaPpa: fila.tarifaPpa,
        consumoEnergia: fila.consumoEnergia,
        generacionEnergia: fila.generacionEnergia,
        costoConsumoSinSsfv: fila.costoConsumoSinSsfv,
        costoRedRemanente: fila.costoRedRemanente,
        costoSsfvPpa: fila.costoSsfvPpa,
        costoSsfvCostoRed: fila.costoSsfvCostoRed,
        ahorroMillones: fila.ahorroMillones
      };

      this._apiService.crudCreate('dtlle-Variables-plantilla', detalle).subscribe({
        next: () => {
          if (huboError) return;
          completados++;
          if (completados === total) {
            this.guardando = false;
            this._alert.success('Proyección guardada exitosamente');
          }
        },
        error: (err) => {
          if (!huboError) {
            huboError = true;
            this.guardando = false;
            console.error(err);
            this._alert.error(this.extractError(err, 'Error guardando los detalles'));
          }
        }
      });
    }
  }

  private extractError(err: any, fallback: string): string {
    if (err?.error?.message) return err.error.message;
    if (typeof err?.error === 'string') return err.error.substring(0, 250);
    return err?.message || fallback;
  }

  volver() {
    this._nav.navigateBack('tabs/admin/variables-plantilla', { animated: true });
  }
}
