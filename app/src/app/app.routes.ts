import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'home',
    loadComponent: () => import('./home/home.page').then((m) => m.HomePage),
  },
  {
    path: '',
    redirectTo: 'home',
    pathMatch: 'full',
  },
  {
    path: 'tabs',
    loadComponent: () => import('./tabs/tabs.page').then( m => m.TabsPage)
  },
  {
    path: 'tabs/us',
    loadComponent: () => import('./pages/registro/registro.page').then( m => m.RegistroPage)
  },
  {
    path: 'registro',
    loadComponent: () => import('./pages/registro/registro.page').then( m => m.RegistroPage)
  },
  {
    path: 'tabs/admin',
    loadComponent: () => import('./pages/admin/admin.page').then( m => m.AdminPage)
  },
  {
    path: 'tabs/admin/clientes',
    loadComponent: () => import('./pages/admin/entities/clientes.page').then( m => m.ClientesPage)
  },
  {
    path: 'tabs/admin/configuracion-empresa',
    loadComponent: () => import('./pages/admin/entities/configuracion-empresa.page').then( m => m.ConfiguracionEmpresaPage)
  },
  {
    path: 'tabs/admin/contactos',
    loadComponent: () => import('./pages/admin/entities/contactos.page').then( m => m.ContactosPage)
  },
  {
    path: 'tabs/admin/planes-ppa',
    loadComponent: () => import('./pages/admin/entities/planes-ppa.page').then( m => m.PlanesPpaPage)
  },
  {
    path: 'tabs/admin/plantillas-documento',
    loadComponent: () => import('./pages/admin/entities/plantillas-documento.page').then( m => m.PlantillasDocumentoPage)
  },
  {
    path: 'tabs/admin/plantilla-secciones',
    loadComponent: () => import('./pages/admin/entities/plantilla-secciones.page').then( m => m.PlantillaSeccionesPage)
  },
  {
    path: 'tabs/admin/plantilla-variables',
    loadComponent: () => import('./pages/admin/entities/plantilla-variables.page').then( m => m.PlantillaVariablesPage)
  },
  {
    path: 'tabs/admin/proyectos',
    loadComponent: () => import('./pages/admin/entities/proyectos.page').then( m => m.ProyectosPage)
  },
  {
    path: 'tabs/admin/roles',
    loadComponent: () => import('./pages/admin/entities/roles.page').then( m => m.RolesPage)
  },
  {
    path: 'tabs/admin/sedes',
    loadComponent: () => import('./pages/admin/entities/sedes.page').then( m => m.SedesPage)
  },
  {
    path: 'tabs/admin/sesiones',
    loadComponent: () => import('./pages/admin/entities/sesiones.page').then( m => m.SesionesPage)
  },
  {
    path: 'tabs/admin/textos-parametrizables',
    loadComponent: () => import('./pages/admin/entities/textos-parametrizables.page').then( m => m.TextosParametrizablesPage)
  },
  {
    path: 'tabs/admin/usuario-roles',
    loadComponent: () => import('./pages/admin/entities/usuario-roles.page').then( m => m.UsuarioRolesPage)
  },
  {
    path: 'tabs/admin/variables-plantilla',
    loadComponent: () => import('./pages/admin/entities/variables-plantilla.page').then( m => m.VariablesPlantillaPage)
  },
  {
    path: 'tabs/admin/visitas',
    loadComponent: () => import('./pages/admin/entities/visitas.page').then( m => m.VisitasPage)
  }
];
