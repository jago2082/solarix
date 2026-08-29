import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule,NavController,ModalController } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators, ValidatorFn, AbstractControl, ValidationErrors } from '@angular/forms';
import { IngresoVhRequest } from 'src/app/models/ingresoVhRequest';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { ApiService } from 'src/app/services/api/api.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';
import { PagarComponent } from 'src/app/components/pagar/pagar.component';

@Component({
  selector: 'app-salidavh',
  templateUrl: './salidavh.page.html',
  styleUrls: ['./salidavh.page.scss'],
  standalone: true,
  imports: [CommonModule, IonicModule, FormsModule, ReactiveFormsModule, HeaderComponent, FooterComponent],
})
export class SalidavhPage implements OnInit {

  private fb = inject(FormBuilder); // ✅ forma correcta en standalone
  form: FormGroup;
  tiposVehiculo = ['Carro', 'Moto'];
  tiposTarifa = ['Fracción', 'Día','Noche', 'Mensualidad'];
  ingCont=0;
  ingtito="";

  constructor( private _sesion : SessionsService,
    private _apiService : ApiService,
    private _alert : AlertService,
    private _nav: NavController,
    private _modal: ModalController
   ) {
    const fechaHoraColombia = this.obtenerFechaHoraColombia();

    this.form = this.fb.group({
      placa: ['', [Validators.required, this.placaValidator()]],
      tipoVehiculo: ['', Validators.required],
      tipoTarifa: ['', Validators.required],
      fechaEntrada: ['', Validators.required],
      horaEntrada: ['', Validators.required],
      fechaSalida: ['', Validators.required],
      horaSalida: ['', Validators.required],
      tiempo: [0, Validators.required],
      subtotal: [0, Validators.required],
      iva: [0, Validators.required],
      pagar: [0, Validators.required],
    });

    // Reactivar validador de placa si cambia el tipo de vehículo
    this.form.get('tipoVehiculo')?.valueChanges.subscribe(() => {
      this.form.get('placa')?.updateValueAndValidity();
    });

  }
  ngOnInit() {
  }


  obtenerFechaHoraColombia(): { fecha: string, hora: string } {
    const fechaBogota = new Date().toLocaleString('en-CA', {
      timeZone: 'America/Bogota',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    });

    const horaBogota = new Date().toLocaleTimeString('en-GB', {
      timeZone: 'America/Bogota',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    });

    return {
      fecha: fechaBogota.split(',')[0], // formato yyyy-MM-dd
      hora: horaBogota // formato HH:mm
    };
  }


  placaValidator(): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const tipoVehiculo = this.form?.get('tipoVehiculo')?.value;
      const placa = control.value?.toUpperCase() || '';

      if (!placa || !tipoVehiculo) return null;

      const carroRegex = /^[A-Z]{3}[0-9]{3}$/;
      const motoRegex = /^[A-Z]{3}[0-9]{2}[A-Z]$/;

      if (tipoVehiculo === 'Carro' && !carroRegex.test(placa)) {
        return { formatoPlaca: 'Formato inválido para carro (ABC123)' };
      }

      if (tipoVehiculo === 'Moto' && !motoRegex.test(placa)) {
        return { formatoPlaca: 'Formato inválido para moto (ABC12D)' };
      }

      return null;
    };
  }

  guardar() {
    let user = JSON.parse(localStorage.getItem("user")!);
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const datos = this.form.getRawValue();
    console.log('Formulario válido:', datos);
    const requestData: IngresoVhRequest = {
      pEmp_codi: this._sesion.GetGnEmpre().emp_codi,
      pIng_tive: datos.tipoVehiculo,
      pIng_Plac: datos.placa,
      pIng_tari: datos.tipoTarifa,
      pCaj_cont: 1,
      pUsu_cont: user.pUsu_Cont,
      pUsu_codi: user.pUsu_codi
    };


    this._apiService.IngresoVh(requestData).subscribe(resp => {
      console.log("Respuesta", resp);
      if (resp.pEstado) {
        this._alert.success("Registro Exitoso");
        this._nav.navigateRoot("tabs", { animated: true });
        
      }else{
        this._alert.error("Error al registrar");
      }   
      
    })


  }

  consultar() {
    const datos = this.form.getRawValue();
      this._apiService.consultaPlaca(datos.placa).subscribe(resp =>{

        console.log("ConsulraPlaca",resp);
        if (resp.pEstado) {
          this.form.patchValue({
            tipoVehiculo: resp.pIng_tive,
            tipoTarifa: resp.pIng_tari,
            fechaEntrada: resp.pIng_feen,
            horaEntrada: resp.pIng_hoen,
            fechaSalida: resp.pIng_fesa,
            horaSalida: resp.pIng_hosa,
            tiempo: resp.pIng_tito,
            subtotal: resp.pIng_vapa,
            iva: resp.pIng_tiva,
            pagar: resp.pIng_tota
          })
          this.ingCont = resp.pIng_cont;
          this.ingtito = resp.pIng_tito;
          
        }else{
          this._alert.error("Error al registrar");
        }
        

      })
    }
    

  campoValido(nombre: string): boolean {
    const campo = this.form.get(nombre);
    return campo?.invalid && campo?.touched || false;
  }

  mensajeErrorPlaca(): string {
    const errors = this.form.get('placa')?.errors;
    if (!errors) return '';
    if (errors['required']) return 'La placa es obligatoria';
    if (errors['formatoPlaca']) return errors['formatoPlaca'];
    return 'Placa inválida';
  }


  async cobrar() {
    const datos = this.form.getRawValue();
    datos.ingtito= this.ingtito;
    datos.ingCont = this.ingCont;
    const modal = await this._modal.create({
      component: PagarComponent,
      componentProps:{
        datos:datos
      }
    });
  
    await modal.present();
  
    await modal.onDidDismiss().then(async resp => {
      if (resp.data != null) {
        this._nav.navigateRoot("tabs", { animated: true });
      }
    })
  }
}
