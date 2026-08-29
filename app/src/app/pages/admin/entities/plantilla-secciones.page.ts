import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { CrudGenericComponent } from '../../crud/crud.page';

@Component({
  selector: 'app-plantilla-secciones',
  template: `<app-crud-generic [entityKey]="'plantilla-secciones'"></app-crud-generic>`,
  standalone: true,
  imports: [CommonModule, IonicModule, CrudGenericComponent]
})
export class PlantillaSeccionesPage {}
