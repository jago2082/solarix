import { Injectable } from '@angular/core';
import { gnconex } from 'src/app/models/gnconex';
import { gnempre } from 'src/app/models/gnempre';
import { developerMode, developerUrl } from 'src/assets/config/config';

@Injectable({
  providedIn: 'root'
})
export class ConfigService {

  constructor() { }

  
  Get(): gnconex {
    if (this.GetDeveloperMode()) {
      const data: gnconex = JSON.parse(localStorage.getItem('GnConex')!);
      data.CNX_IPSR = this.GetDeveloperMode().url;
      let company: gnempre = { emp_codi: this.GetDeveloperMode().companyCode, emp_nomb: 'Empresa de pruebas' };
      localStorage.setItem('GnEmpre', JSON.stringify(company));
      return data;
    }

    if (developerMode) {
      const data: gnconex = JSON.parse(localStorage.getItem('GnConex')!);
      data.CNX_IPSR = developerUrl;
      return data;
    }
    else {
      return JSON.parse(localStorage.getItem('GnConex')!);
    }
  }

  SetDeveloperMode(baseUrl: string, companyCode: number) {
    let data = { url: baseUrl, companyCode: companyCode };
    console.log(data);
    localStorage.setItem("developerMode", JSON.stringify(data));
  }

  GetDeveloperMode() {
    const devMode = localStorage.getItem("developerMode");
    return devMode ? JSON.parse(devMode) : null;
}

}
