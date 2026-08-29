import { Component, Input, OnInit } from '@angular/core';
import { debounceTime, distinctUntilChanged, switchMap } from 'rxjs/operators';
import { Subject } from 'rxjs';
import { ApiService } from 'src/app/services/api/api.service';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { IonicModule,ModalController } from '@ionic/angular';
import { confirmarSalidaRequest } from 'src/app/models/confirmarSalidaRequest';
import { SessionsService } from 'src/app/services/sessions/sessions.service';

@Component({
  selector: 'app-pagar',
  templateUrl: './pagar.component.html',
  styleUrls: ['./pagar.component.scss'],
  standalone: true,
  imports: [CommonModule, IonicModule, ReactiveFormsModule,FormsModule ] // Agrega aquí IonicModule, CommonModule, FormsModule, etc.
})
export class PagarComponent implements OnInit {

  @Input() datos: any;
  cambio: number = 0;
  searchTerm: string = '';
  resultados: any[] = [];
  showSuggestions: boolean = false;
  usuarioSeleccionado: any = null;
  pagoValido: boolean = false;
  valorAPagar= 0;

  pagos = {
    efectivo: 0,
    nequi: 0,
    tarjeta: 0,
    daviplata: 0
  };

  gStTipoPago: string = '';
  gInVen_fopa: number = 0;
  gInVen_mepa: number = 0;

  errorPago: string = '';

  private searchSubject = new Subject<string>();

  constructor(private apiService: ApiService,
    private _modal: ModalController,
    private _sesion : SessionsService
  ) {
    this.searchSubject.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap(term => this.apiService.Consultarclientes(term))
    ).subscribe(data => {
      this.resultados = data;
      this.showSuggestions = true;
    });
  }


  ngOnInit()
  {
    console.log("Input",this.datos);
    
    this.valorAPagar = this.datos.pagar;
  }

  onSearchInput() {
    if (this.searchTerm && this.searchTerm.length >= 3) {
      this.searchSubject.next(this.searchTerm);
    } else {
      this.resultados = [];
      this.showSuggestions = false;
    }
  }

  seleccionarUsuario(usuario: any) {
    this.usuarioSeleccionado = usuario;
    this.searchTerm = `${usuario.pCli_docu} - ${usuario.pCli_noco}`;
    this.resultados = [];
    this.showSuggestions = false;
  }

  confirmarPago() {
    const totalPagado = this.pagos.efectivo + this.pagos.nequi + this.pagos.tarjeta + this.pagos.daviplata;
  
    if (totalPagado < this.valorAPagar) {
      this.errorPago = `El total pagado ($${totalPagado}) no puede ser menor al valor a pagar ($${this.valorAPagar})`;
      this.cambio = 0;
    } else {
      this.errorPago = '';
      this.cambio = totalPagado - this.valorAPagar;
      // Aquí puedes emitir un evento o continuar con el proceso de pago
      //console.log('Pago confirmado:', this.pagos);
      this.tipoPago();
      let user = JSON.parse(localStorage.getItem("user")!);
      const confirmarSalida: confirmarSalidaRequest={
        pEmp_codi:this._sesion.GetGnEmpre().emp_codi,
        pIng_cont: this.datos.ingCont,
        pIng_fesa: this.datos.fechaSalida,
        pIng_hosa: this.datos.horaSalida,
        pIng_vapa: this.datos.subtotal,
        pIng_tiva: this.datos.iva,
        pCli_cont: this.usuarioSeleccionado.pCli_cont,
        pVen_sald: this.cambio,
        pVen_vale: this.pagos.efectivo,
        pCaj_cont:1,
        pUsu_codi: user.pUsu_codi,
        pVen_vefe: this.pagos.efectivo,
        pVen_vtar:this.pagos.tarjeta,
        pVen_vnqu :this.pagos.nequi,
        pVen_vdpl:this.pagos.daviplata,
        pVen_fopa:this.gInVen_fopa,
        pVen_mepa:this.gInVen_mepa,
        pIng_tito:this.datos.ingtito,
        pVen_tpag:this.gStTipoPago,
        pUsu_cont:user.pUsu_Cont
      }

      this.apiService.ConfirmarSalida(confirmarSalida).subscribe((resp:any)=>{
        console.log("ConfirmarSalida",resp);
          if (resp.pEstado) {
            this._modal.dismiss(resp.pEstado);
          }       
      })

    }
  }

  CerrarModal() {
    this._modal.dismiss();
  }


  actualizarCambio() {
    const totalPagado = this.pagos.efectivo + this.pagos.nequi + this.pagos.tarjeta + this.pagos.daviplata;
  
    if (totalPagado < this.valorAPagar) {
      this.cambio = 0;
      this.errorPago = `Falta dinero. Pagado: $${totalPagado}, Falta: $${this.valorAPagar - totalPagado}`;
      this.pagoValido = false;
    } else {
      this.cambio = Math.floor(totalPagado - this.valorAPagar);
      this.errorPago = '';
      this.pagoValido = true;
    }
  }
  tipoPago() {
    let lInEfectivo = 6;
    let lInTarjeta = 5;
    let lInNequi = 8;
    let lInDaviplata = 9;
  
    if (!this.pagos.efectivo || this.pagos.efectivo === 0) {
      lInEfectivo = 0;
    }
  
    if (!this.pagos.tarjeta || this.pagos.tarjeta === 0) {
      lInTarjeta = 0;
    }
  
    if (!this.pagos.nequi || this.pagos.nequi === 0) {
      lInNequi = 0;
    }
  
    if (!this.pagos.daviplata || this.pagos.daviplata === 0) {
      lInDaviplata = 0;
    }
  
    const lInCalculo = lInEfectivo + lInTarjeta + lInNequi + lInDaviplata;
  
    if (lInCalculo === 6) {
      this.gStTipoPago = 'E';
      this.gInVen_fopa = 1;
      this.gInVen_mepa = 10;
    } else if (lInCalculo === 5) {
      this.gStTipoPago = 'T';
      this.gInVen_fopa = 1;
      this.gInVen_mepa = 49;
    } else if (lInCalculo === 4) {
      this.gStTipoPago = 'C';
      this.gInVen_fopa = 0;
      this.gInVen_mepa = 0;
    } else if (lInCalculo === 8) {
      this.gStTipoPago = 'N';
      this.gInVen_fopa = 1;
      this.gInVen_mepa = 31;
    } else if (lInCalculo === 9) {
      this.gStTipoPago = 'D';
      this.gInVen_fopa = 1;
      this.gInVen_mepa = 31;
    } else if (lInCalculo > 9) {
      this.gStTipoPago = 'M';
      this.gInVen_fopa = 1;
      this.gInVen_mepa = 1;
    }
  }
  
  

}