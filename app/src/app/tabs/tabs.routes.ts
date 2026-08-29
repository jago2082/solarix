import { Routes } from '@angular/router';
import { TabsPage } from './tabs.page';

export const routes: Routes = [
  {
    path: '',
    component: TabsPage,
    children: [
      {
        path: 'in',
        loadComponent: () =>
          import('../pages/entradavh/entradavh.page').then((m) => m.EntradavhPage),
      },
      {
        path: 'sa',
        loadComponent: () =>
          import('../pages/salidavh/salidavh.page').then((m) => m.SalidavhPage),
      },
      {
        path: 'pe',
        loadComponent: () =>
          import('../pages/pendientesvh/pendientesvh.page').then((m) => m.PendientesvhPage),
      },
      {
        path: 'us',
        loadComponent: () =>
          import('../pages/registro/registro.page').then((m) => m.RegistroPage),
      },
      {
        path: 'admin',
        loadComponent: () =>
          import('../pages/admin/admin.page').then((m) => m.AdminPage),
      },
      {
        path: 'admin/clientes',
        loadComponent: () => import('../pages/admin/entities/clientes.page').then((m) => m.ClientesPage),
      },
      {
        path: 'admin/configuracion-empresa',
        loadComponent: () => import('../pages/admin/entities/configuracion-empresa.page').then((m) => m.ConfiguracionEmpresaPage),
      },
      {
        path: 'admin/contactos',
        loadComponent: () => import('../pages/admin/entities/contactos.page').then((m) => m.ContactosPage),
      },
      {
        path: 'admin/planes-ppa',
        loadComponent: () => import('../pages/admin/entities/planes-ppa.page').then((m) => m.PlanesPpaPage),
      },
      {
        path: 'admin/plantillas-documento',
        loadComponent: () => import('../pages/admin/entities/plantillas-documento.page').then((m) => m.PlantillasDocumentoPage),
      },
      {
        path: 'admin/plantilla-secciones',
        loadComponent: () => import('../pages/admin/entities/plantilla-secciones.page').then((m) => m.PlantillaSeccionesPage),
      },
      {
        path: 'admin/plantilla-variables',
        loadComponent: () => import('../pages/admin/entities/plantilla-variables.page').then((m) => m.PlantillaVariablesPage),
      },
      {
        path: 'admin/proyectos',
        loadComponent: () => import('../pages/admin/entities/proyectos.page').then((m) => m.ProyectosPage),
      },
      {
        path: 'admin/roles',
        loadComponent: () => import('../pages/admin/entities/roles.page').then((m) => m.RolesPage),
      },
      {
        path: 'admin/sedes',
        loadComponent: () => import('../pages/admin/entities/sedes.page').then((m) => m.SedesPage),
      },
      {
        path: 'admin/sesiones',
        loadComponent: () => import('../pages/admin/entities/sesiones.page').then((m) => m.SesionesPage),
      },
      {
        path: 'admin/textos-parametrizables',
        loadComponent: () => import('../pages/admin/entities/textos-parametrizables.page').then((m) => m.TextosParametrizablesPage),
      },
      {
        path: 'admin/usuario-roles',
        loadComponent: () => import('../pages/admin/entities/usuario-roles.page').then((m) => m.UsuarioRolesPage),
      },
      {
        path: 'admin/variables-plantilla',
        loadComponent: () => import('../pages/admin/entities/variables-plantilla.page').then((m) => m.VariablesPlantillaPage),
      },
      {
        path: 'admin/visitas',
        loadComponent: () => import('../pages/admin/entities/visitas.page').then((m) => m.VisitasPage),
      },
      {
        path: '',
        redirectTo: 'in',
        pathMatch: 'full',
      }
    ],
  },
];
