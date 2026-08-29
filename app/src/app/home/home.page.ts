import { Component, OnInit } from '@angular/core';
import { IonicModule,AlertController,LoadingController, NavController } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../services/api/api.service';
import { ConfigService } from '../services/config/config.service';
import { SessionsService } from '../services/sessions/sessions.service';
import { AlertService } from '../services/alert/alert.service';
import { loginRequest } from '../models/loginrequest';
import { settings } from '../../assets/config/config';




@Component({
  standalone: true,
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  imports: [IonicModule, CommonModule, FormsModule], // Importa IonicModule aquí
})
export class HomePage implements OnInit {

  user: loginRequest = new loginRequest();
  loading = false;
  usuarios: any;
  emp_codi = 0;
  _loading: any;


  constructor( private _apiService: ApiService,
    public alertCtrl: AlertController,
    private configService: ConfigService,
    private _sesion: SessionsService,
    private _loadingCtrl: LoadingController,
    private _alert: AlertService,
    private _nav: NavController
   ) {}

   async ngOnInit() {
    await this.GetGnConex();
    
  }

  async GetGnConex() {
    this._sesion.SetGnConex(settings);
    this._sesion.SetGnEmpre({
      emp_codi:101,
      emp_nomb:"Servicentro el Bosque"
    });
    
  }

    
  async login() {
    this.loading = true;
    const loading = await this._loadingCtrl.create({ message: 'Ingresando...' });
    await loading.present();

    this._apiService.signIn(this.user).subscribe({
      next: async (resp) => {
        this.loading = false;
        await loading.dismiss();
        console.log(resp);
        if (resp && resp.status === 'success' && resp.token) {
          localStorage.setItem("token", resp.token);
          this._sesion.SetRol(resp.rol);
          this._alert.success(resp.message);
          const target = this._sesion.IsAdmin() ? 'tabs/admin' : 'tabs/in';
          this._nav.navigateRoot(target, { animated: true });
        } else {
          this._alert.error(resp?.message || "Ingreso fallido");
        }
      },
      error: async (err: string) => {
        this.loading = false;
        await loading.dismiss();
        this._alert.error(err);
      }
    });
  }


  async getPass() {
    let alert = await this.alertCtrl.create({
      header: 'Llave de configuración',
      message: 'Ingrese la llave de configuración',
      inputs: [
        {
          name: 'password',
          type: 'password'
        }],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'btn-cancelar',
          handler: () => {
            console.log('Confirm Cancel');
          }
        }, {
          text: 'Ok',
          cssClass: 'btn-confirmar',
          handler: (alertData:any) => {
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
        }],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'btn-cancelar',
          handler: () => {
            console.log('Confirm Cancel');
          }
        }, {
          text: 'Ok',
          cssClass:'btn-confirmar',
          handler: (alertData:any) => {
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
        }],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'btn-cancelar',
          handler: () => {
            console.log('Confirm Cancel');
          }
        }, {
          text: 'Ok',
          cssClass:'btn-confirmar',
          handler: (alertData:any) => {
            this.emp_codi = alertData.emp_codi;
            console.log(url);
            this.configService.SetDeveloperMode(url, this.emp_codi);
          }
        }
      ]
    });
    await alert.present();
  }


}