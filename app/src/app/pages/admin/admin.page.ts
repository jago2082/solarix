import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, NavController } from '@ionic/angular';
import { EntityConfigService, EntityConfig } from 'src/app/services/entity-config/entity-config.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.page.html',
  styleUrls: ['./admin.page.scss'],
  standalone: true,
  imports: [CommonModule, IonicModule, HeaderComponent, FooterComponent]
})
export class AdminPage implements OnInit {
  entities: EntityConfig[] = [];

  constructor(
    private _entityConfig: EntityConfigService,
    private _alert: AlertService,
    private _nav: NavController,
    private _sesion: SessionsService
  ) {}

  ngOnInit() {
    if (!this._sesion.IsAdmin()) {
      this._alert.error('No tiene permisos para acceder a esta opción');
      this._nav.navigateRoot('tabs/in', { animated: true });
      return;
    }
    this.entities = this._entityConfig.getAll().filter(e => e.key !== 'usuarios');
  }

  goToUsers() {
    this._nav.navigateForward('tabs/us', { animated: true });
  }

  goToEntity(entity: EntityConfig) {
    this._nav.navigateForward(`tabs/admin/${entity.key}`, { animated: true });
  }
}
