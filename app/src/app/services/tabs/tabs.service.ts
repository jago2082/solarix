import { Injectable } from '@angular/core';
import { mainMenu } from 'src/app/models/menus';
import { SessionsService } from '../sessions/sessions.service';

@Injectable({
  providedIn: 'root'
})
export class TabsService {

  constructor(private _sesion: SessionsService) { }

  getTabs(): mainMenu[] {
    const tabs: mainMenu[] = [
      {
        mainIcon: "",
        mainTitle: "Entrada Vehiculos",
        mainPath: "in",
        mainSrc: "assets/icons/entradavh.svg",
        disable:false
      },
      {
        mainIcon: "",
        mainTitle: "Salida Vehiculos",
        mainPath: "sa",
        mainSrc: "assets/icons/salidavh.svg",
        disable:false
      },
      {
        mainIcon: "",
        mainTitle: "Vehiculos Pendientes",
        mainPath: "pe",
        mainSrc: "assets/icons/vhpendientes.svg",
        disable:false
      }
    ];

    if (this._sesion.IsAdmin()) {
      tabs.push({
        mainIcon: "",
        mainTitle: "Admin",
        mainPath: "admin",
        mainSrc: "assets/icons/settings.svg",
        disable: false
      });
    }

    return tabs;
  }

}
