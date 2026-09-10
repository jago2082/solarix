import { CommonModule } from '@angular/common';
import { Component, OnInit, Input } from '@angular/core';
import { Router } from '@angular/router';
import { IonicModule, NavController, AlertController } from '@ionic/angular';
import { ApiService } from 'src/app/services/api/api.service';
import { ConfigService } from 'src/app/services/config/config.service';

@Component({
  standalone: true,
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.scss'],
  imports: [IonicModule, CommonModule]
})
export class FooterComponent implements OnInit {
  @Input() hideBack = false;
  data: any[] = [];
  count = 0;
  emp_codi = 101;

  constructor(
    private _rout: Router,
    private _nav: NavController,
    private _apiServices: ApiService,
    private configService: ConfigService,
    public alertCtrl: AlertController
  ) {}

  ngOnInit() {}

  // MÉTODO PARA EL BOTÓN DE ATRÁS
  goBack() {
    // Intenta regresar a la vista anterior en el historial de navegación
    this._nav.back();
  }

  logged() {
    return localStorage.getItem('token') !== null;
  }

  goMenu() {
    this._nav.navigateForward('tabs/menu');
  }

  goProfile() {
    this._rout.navigateByUrl('tabs/profile');
  }

  async getPass() {
    let alert = await this.alertCtrl.create({
      header: 'Llave de configuración',
      message: 'Ingrese la llave de configuración',
      inputs: [
        {
          name: 'password',
          type: 'password'
        }
      ],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
            console.log('Confirm Cancel');
          }
        },
        {
          text: 'Ok',
          handler: (alertData: any) => {
            if (alertData.password == "sistemas") {
              console.log(alertData.password);
              this.changeURI();
            }
          }
        }
      ]
    });
    await alert.present();
  }

  async changeURI() {
    let alert = await this.alertCtrl.create({
      header: 'Ajustes de apuntamiento',
      message: 'Defina la url a la que apuntará la aplicación. El direccionamiento debe seguir la siguiente nomenclarura: http://servidor/sitio/api/',
      inputs: [
        {
          name: 'URL',
          type: 'url'
        }
      ],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
            console.log('Confirm Cancel');
          }
        },
        {
          text: 'Ok',
          handler: (alertData: any) => {
            this.changeEmpCodi(alertData.URL);
          }
        }
      ]
    });
    await alert.present();
  }

  async changeEmpCodi(url: string) {
    console.log(url);
    let alert = await this.alertCtrl.create({
      header: 'Código de empresa',
      message: 'Defina el código de empresa con el cual hará el ingreso',
      inputs: [
        {
          name: 'emp_codi',
          type: 'number'
        }
      ],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
            console.log('Confirm Cancel');
          }
        },
        {
          text: 'Ok',
          handler: (alertData: any) => {
            this.emp_codi = alertData.emp_codi;
            console.log(url);
            this.configService.SetDeveloperMode(url, this.emp_codi);
          }
        }
      ]
    });
    await alert.present();
  }

  goOut() {
    this._rout.navigateByUrl("/home");
    localStorage.removeItem("token");
    localStorage.removeItem("rol");
    localStorage.removeItem("user");
  }
}