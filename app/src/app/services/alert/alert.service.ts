import { Injectable } from '@angular/core';
import { AlertController } from '@ionic/angular';
import Swal from 'sweetalert2';

@Injectable({
  providedIn: 'root'
})
export class AlertService {

  constructor( private _alert: AlertController ) { }


  async showAlert(title: string, message: string) {
    let alert = await this._alert.create({
      header: title,
      message: message,
      buttons: ["OK"]
    });
    alert.present();
  }

  async showAlertConfirmAcept(msg: string, title: string) {
    return new Promise(async resolve => {
      const alert = await this._alert.create({
        buttons: [
          {
            text: "Aceptar",
            handler: ok => {
              resolve(true);
            }
          }
        ],
        message: msg,
        header: title
      });

      await alert.present();
    })
  }

  async showAlertConfirm(msg: string, title: string) {
    return new Promise(async resolve => {
      const alert = await this._alert.create({
        buttons: [
          {
            text: "Cancelar",
            role: "cancel",
            cssClass: "secondary",
            handler: () => {
              resolve(false);
            },
          },
          {
            text: "Si",
            handler: ok => {
              resolve(true);
            }
          }
        ],
        message: msg,
        header: title
      });

      await alert.present();
    })
  }

  success(message: string) {
    Swal.fire({
      heightAuto: false,
      title: 'Genial!',
      text: message,
      icon: 'success',
      confirmButtonText: 'Aceptar',
      confirmButtonColor:'#d36217'
    })
  }

  info(message: string) {
    Swal.fire({
      heightAuto: false,
      title: 'Información',
      text: message,
      icon: 'info',
      confirmButtonText: 'Aceptar',
      confirmButtonColor:'#d36217'
    })
  }

  error(message: string) {
    Swal.fire({
      heightAuto: false,
      title: 'Oops!',
      text: message,
      icon: 'error',
      confirmButtonText: 'Aceptar',
      confirmButtonColor:'#d36217'
    })
  }

  warning(message: string) {
    Swal.fire({
      heightAuto: false,
      title: 'Alerta!',
      text: message,
      icon: 'warning',
      confirmButtonText: 'Aceptar',
      confirmButtonColor:'#d36217'
    })
  }


}
