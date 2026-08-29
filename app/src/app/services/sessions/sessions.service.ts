import { Injectable } from '@angular/core';
import { gnconex } from 'src/app/models/gnconex';
import { gnempre } from 'src/app/models/gnempre';

@Injectable({
  providedIn: 'root'
})
export class SessionsService {

  constructor() { }


  SetGnConex(business: gnconex) {
    localStorage.setItem('GnConex', JSON.stringify(business));
  }

  GetGnConex() {
   return JSON.parse(localStorage.getItem('GnConex')!);
  }

  SetGnEmpre(business: gnempre) {
    localStorage.setItem('GnEmpre', JSON.stringify(business));
  }

  GetGnEmpre(): gnempre {
    return JSON.parse(localStorage.getItem('GnEmpre')!);
  }

  SetRol(rol?: string) {
    if (rol) {
      localStorage.setItem('rol', rol);
    }
  }

  GetRol() {
    return localStorage.getItem('rol');
  }

  IsAdmin(): boolean {
    const rol = this.GetRol();
    return rol === 'SUPERADMIN' || rol === 'ADMIN';
  }

  ClearSession() {
    localStorage.removeItem('token');
    localStorage.removeItem('rol');
  }

}
