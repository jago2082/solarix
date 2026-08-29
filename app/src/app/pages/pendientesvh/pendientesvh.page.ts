import { Component, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {IonicModule} from '@ionic/angular';
import { ApiService } from 'src/app/services/api/api.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { PaginatorModule } from 'primeng/paginator'; // opcional, incluido en TableModule internamente
import { Table } from 'primeng/table';
import { FooterComponent } from 'src/app/components/footer/footer.component';

@Component({
  selector: 'app-pendientesvh',
  templateUrl: './pendientesvh.page.html',
  styleUrls: ['./pendientesvh.page.scss'],
  standalone: true,
  imports: [IonicModule, CommonModule, FormsModule,HeaderComponent,TableModule,InputTextModule,PaginatorModule, FooterComponent]
})
export class PendientesvhPage implements OnInit {
  @ViewChild('table') table!: Table;
  globalFilter: string = '';
  data: any[] = [];

  constructor( private _apiService : ApiService) { }

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    this._apiService.ConsultaVehiculosPendientes().subscribe((res:any) =>{

      console.log("Usuarios",res);
      res.forEach((element:any) => {
        const item:any={
          pIng_tive:element.pIng_tive,
          pIng_Plac:element.pIng_Plac,
          pIng_tari:element.pIng_tari,
          pIng_feen:element.pIng_feen.substring(0, 10),
          pIng_hoen:element.pIng_hoen
        }
        this.data.push(item);  
      });
      
    }) 
  }

  aplicarFiltroGlobal(event: Event) {
    const input = event.target as HTMLInputElement;
    this.table?.filterGlobal(input.value, 'contains');
  }
  
}
