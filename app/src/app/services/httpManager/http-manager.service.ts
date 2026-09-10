import {
  HttpClient,
  HttpHeaders,
  HttpErrorResponse
} from "@angular/common/http";
import { Injectable } from '@angular/core';
import { AlertService } from '../alert/alert.service';
import { catchError, retry, tap, throwError } from 'rxjs';
import { ConfigService } from "../config/config.service";

@Injectable({
  providedIn: 'root'
})
export class HttpManagerService {

  baseUrl!: string;
  centralizacionUrl!: string;
  private httpOptions!: {
    headers: HttpHeaders;
  };
  strToken = "";
  
  constructor(private _http: HttpClient,
    private configService: ConfigService,
    private _alert: AlertService) {
  }

  Get<T>(urlController: string, strToken?: string) {
    this.baseUrl =  this.configService.Get().CNX_IPSR;
    const token = strToken || localStorage.getItem('token') || '';
    console.log(token);
    
    // CORRECCIÓN: Eliminamos los headers de CORS aquí
    const headerAnonimous = {
      "Content-Type": "application/json",
      Accept: "application/json"
    };
    
    const headerAuth = {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: token ? `Bearer ${token}` : ''
    };

    let headers = new HttpHeaders();
    if (token) {
      headers = new HttpHeaders(headerAuth);
    } else {
      headers = new HttpHeaders(headerAnonimous);
    }

    let options: any = {
      headers: headers,
      observe: "body"
    };

    let url = `${this.baseUrl}${urlController}`;
    console.log(options);
    console.log(url);
    return this._http.get<T>(url, <object>options).pipe(
      tap(resp => {
        retry(3), // reintenta la petición 3 veces
          console.log(resp);
      }), catchError(err => this.handleError(err))
    );
  }

  Post<T>(urlController: string, body?: any, strToken?: string) {
    this.baseUrl = this.configService.Get().CNX_IPSR;
    const token = strToken || localStorage.getItem('token') || '';
    
    // CORRECCIÓN: Eliminamos los headers de CORS aquí también
    const headerAnonimous = {
      "Content-Type": "application/json",
      Accept: "application/json"
    };
    
    const headerAuth = {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: token ? `Bearer ${token}` : ''
    };
    
    let headers = new HttpHeaders();
    if (token) {
      headers = new HttpHeaders(headerAuth);
    } else {
      headers = new HttpHeaders(headerAnonimous);
    }
    
    let options: any = {
      headers: headers,
      observe: "body"
    };
    
    console.log(`${this.baseUrl}${urlController}`);
    console.log(body);
    
    return this._http
      .post<T>(`${this.baseUrl}${urlController}`, body, <object>options)
      .pipe(
        tap(resp => {
          retry(3);
          console.log(resp);
        })
        , catchError(err => this.handleError(err))
      );
  }

  GetGeneric<T>(urlController:string){
    return this._http.get<T>(`${this.baseUrl}${urlController}`);
  }

  Put<T>(urlController: string, body?: any, strToken?: string) {
    this.baseUrl = this.configService.Get().CNX_IPSR;
    const token = strToken || localStorage.getItem('token') || '';

    const headerAnonimous = {
      "Content-Type": "application/json",
      Accept: "application/json"
    };

    const headerAuth = {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: token ? `Bearer ${token}` : ''
    };

    let headers = new HttpHeaders();
    if (token) {
      headers = new HttpHeaders(headerAuth);
    } else {
      headers = new HttpHeaders(headerAnonimous);
    }

    let options: any = {
      headers: headers,
      observe: "body"
    };

    return this._http
      .put<T>(`${this.baseUrl}${urlController}`, body, <object>options)
      .pipe(
        tap(resp => {
          retry(3);
          console.log(resp);
        })
        , catchError(err => this.handleError(err))
      );
  }

  Delete<T>(urlController: string, strToken?: string) {
    this.baseUrl = this.configService.Get().CNX_IPSR;
    const token = strToken || localStorage.getItem('token') || '';

    const headerAnonimous = {
      "Content-Type": "application/json",
      Accept: "application/json"
    };

    const headerAuth = {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: token ? `Bearer ${token}` : ''
    };

    let headers = new HttpHeaders();
    if (token) {
      headers = new HttpHeaders(headerAuth);
    } else {
      headers = new HttpHeaders(headerAnonimous);
    }

    let options: any = {
      headers: headers,
      observe: "body"
    };

    return this._http
      .delete<T>(`${this.baseUrl}${urlController}`, <object>options)
      .pipe(
        tap(resp => {
          retry(3);
          console.log(resp);
        })
        , catchError(err => this.handleError(err))
      );
  }

  private handleError(error: HttpErrorResponse) {
    console.log('entra a error');
    if (error.error instanceof ErrorEvent) {
      console.error("Ocurrió un error:", error.error.message);
    } else {
      console.error(`Backend returned code ${error.status}`);
      console.error('Response body:', error.error);
      if (error.error?.message) console.error('Backend message:', error.error.message);
      if (error.error?.details) console.error('Backend details:', error.error.details);

      if (error.status == 401) {
        console.log('saliendo...');
        this._alert.showAlert('Acceso no autorizado', 'Autenticación no válida con el servicio. Ingrese nuevamente')
      } else {
        this._alert.showAlert('Error de conexión', `Código de error: ${error.status}`);
      }
    }
    return throwError(
      "Ocurrió un error inesperado.Inténtelo nuevamente más tarde"
    );
  }
}