import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { CrudGenericComponent } from '../../crud/crud.page';

@Component({
  selector: 'app-detalle-variables-plantilla',
  template: `<app-crud-generic [entityKey]="'detalle-variables-plantilla'"></app-crud-generic>`,
  standalone: true,
  imports: [CommonModule, IonicModule, CrudGenericComponent]
})
export class DetalleVariablesPlantillaPage {}
