import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, NavController } from '@ionic/angular';
import { EntityConfigService, EntityConfig } from 'src/app/services/entity-config/entity-config.service';
import { AlertService } from 'src/app/services/alert/alert.service';
import { SessionsService } from 'src/app/services/sessions/sessions.service';
import { HeaderComponent } from 'src/app/components/header/header.component';
import { FooterComponent } from 'src/app/components/footer/footer.component';

// 1. Importar addIcons y la lista completa de iconos
import { addIcons } from 'ionicons';
import { 
  people, 
  briefcase, 
  build, 
  book, 
  documentText, 
  copy, 
  layers, 
  codeWorking, 
  folderOpen, 
  shieldCheckmark, 
  business, 
  time, 
  text, 
  personAdd, 
  statsChart, 
  clipboard, 
  grid 
} from 'ionicons/icons';

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
  ) {
    // 2. Registrar todos los iconos en el constructor
    addIcons({
      'people': people,
      'briefcase': briefcase,
      'build': build,
      'book': book,
      'document-text': documentText,
      'copy': copy,
      'layers': layers,
      'code-working': codeWorking,
      'folder-open': folderOpen,
      'shield-checkmark': shieldCheckmark,
      'business': business,
      'time': time,
      'text': text,
      'person-add': personAdd,
      'stats-chart': statsChart,
      'clipboard': clipboard,
      'grid': grid
    });
  }

  ngOnInit() {
    if (!this._sesion.IsAdmin()) {
      this._alert.error('No tiene permisos para acceder a esta opción');
      this._nav.navigateRoot('tabs/in', { animated: true });
      return;
    }

    const orden: Record<string, number> = {
      'roles': 1,
      'usuario-roles': 2,
      'clientes': 3,
      'contactos': 4,
      'sedes': 5,
      'visitas': 6,
      'proyectos': 7,
      'planes-ppa': 8,
      'variables-plantilla': 9,
      'plantillas-documento': 10,
      'plantilla-secciones': 11,
      'plantilla-variables': 12,
      'textos-parametrizables': 13,
      'configuracion-empresa': 14
    };

    this.entities = this._entityConfig.getAll()
      .filter(e => e.key !== 'usuarios' && !e.hidden)
      .sort((a, b) => (orden[a.key] ?? 99) - (orden[b.key] ?? 99));
  }

  // 3. Mapeo completo con las tarjetas faltantes
  getIconForEntity(nombre: string): string {
    if (!nombre) return 'grid';

    const nameClean = nombre.trim();

    const iconMap: { [key: string]: string } = {
      'Clientes': 'briefcase',
      'Configuraciones de empresa': 'build',
      'Contactos': 'book',
      'Planes PPA': 'document-text',
      'Plantillas de documento': 'copy',
      'Secciones de plantilla': 'layers',
      'Variables de plantilla': 'code-working',
      'Proyectos': 'folder-open',
      'Roles': 'shield-checkmark',
      'Sedes': 'business',
      'Sesiones': 'time',
      'Textos parametrizables': 'text',
      'Asignaciones usuario-rol': 'person-add',
      'Variables de proyección': 'stats-chart',
      'Visitas técnicas': 'clipboard'
    };

    return iconMap[nameClean] || 'grid';
  }

  goToUsers() {
    this._nav.navigateForward('tabs/us', { animated: true });
  }

  goToEntity(entity: EntityConfig) {
    this._nav.navigateForward(`tabs/admin/${entity.key}`, { animated: true });
  }
}