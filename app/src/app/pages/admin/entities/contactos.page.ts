import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { CrudGenericComponent } from '../../crud/crud.page';

@Component({
  selector: 'app-contactos',
  template: `<app-crud-generic [entityKey]="'contactos'"></app-crud-generic>`,
  standalone: true,
  imports: [CommonModule, IonicModule, CrudGenericComponent]
})
export class ContactosPage {}
