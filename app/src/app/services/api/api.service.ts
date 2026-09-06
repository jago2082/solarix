import { Injectable } from '@angular/core';
import { HttpManagerService } from '../httpManager/http-manager.service';
import { SessionsService } from '../sessions/sessions.service';
import { loginRequest } from 'src/app/models/loginrequest';
import { IngresoVhRequest } from 'src/app/models/ingresoVhRequest';
import { confirmarSalidaRequest } from 'src/app/models/confirmarSalidaRequest';
import { RegistroUsuarioRequest } from 'src/app/models/registroUsuarioRequest';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  
  constructor(
    private _http: HttpManagerService,
    private _sesion: SessionsService
  ) { }

  getUsers() {
    return this._http.Get<any>("api/usuarios");
  }

  signIn(credentials: loginRequest) {
    return this._http.Post<any>("api/usuarios/login", credentials);
  }

  IngresoVh(requestData: IngresoVhRequest) {
    return this._http.Post<any>("IngresoVh", requestData);
  }

  ConsultaVehiculosPendientes() {
    return this._http.Get<any>(`ConsultaVehiculosPendientes?pEmp_codi=${this._sesion.GetGnEmpre().emp_codi}`);
  }

  loadUser(): any {
    let user = JSON.parse(localStorage.getItem('user')!);
    return user;
  }

  consultaPlaca(placa: any) {
    return this._http.Get<any>(`ConsultaPlaca?pEmp_codi=${this._sesion.GetGnEmpre().emp_codi}&pIngPlac=${placa}`);
  }

  Consultarclientes(term: string) {
    return this._http.Get<any>(`ConsultaClientesApp?pEmp_codi=${this._sesion.GetGnEmpre().emp_codi}&p_cli_pabu=${term}`);
  }

  ConfirmarSalida(confirmarSalida: confirmarSalidaRequest) {
    return this._http.Post<any>("ConfirmarSalida", confirmarSalida);
  }

  registrarUsuario(requestData: RegistroUsuarioRequest) {
    return this._http.Post<any>("api/usuarios", requestData);
  }

  actualizarUsuario(id: string | number, requestData: RegistroUsuarioRequest) {
    return this._http.Put<any>(`api/usuarios/${id}`, requestData);
  }

  eliminarUsuario(id: string | number) {
    return this._http.Delete<any>(`api/usuarios/${id}`);
  }

  /**
   * Helper interno para limpiar duplicados en endpoints (evita api/api/...)
   */
  private getCleanEndpoint(endpoint: string): string {
    return endpoint.startsWith('api/') ? endpoint.replace(/^api\//, '') : endpoint;
  }

  crudGetAll(endpoint: string) {
    const clean = this.getCleanEndpoint(endpoint);
    return this._http.Get<any>(`api/${clean}`);
  }

  crudCreate(endpoint: string, body: any) {
    const clean = this.getCleanEndpoint(endpoint);
    return this._http.Post<any>(`api/${clean}`, body);
  }

  crudUpdate(endpoint: string, id: string | number, body: any) {
    const clean = this.getCleanEndpoint(endpoint);
    return this._http.Put<any>(`api/${clean}/${id}`, body);
  }

  crudDelete(endpoint: string, id: string | number) {
    const clean = this.getCleanEndpoint(endpoint);
    return this._http.Delete<any>(`api/${clean}/${id}`);
  }

}