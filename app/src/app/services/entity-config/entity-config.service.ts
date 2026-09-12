import { Injectable } from '@angular/core';

export interface SelectOption {
  label: string;
  value: any;
}

export interface CrudField {
  key: string;
  label: string;
  type: 'text' | 'number' | 'email' | 'password' | 'select' | 'textarea' | 'date' | 'datetime-local' | 'boolean';
  required?: boolean;
  requiredOnCreate?: boolean;
  options?: SelectOption[];
  source?: { endpoint: string; valueField: string; labelField: string };
  showInTable?: boolean;
  /** Campo calculado: se oculta en el formulario y su valor se genera automáticamente */
  computed?: boolean;
}

export interface EntityConfig {
  key: string;
  endpoint: string;
  label: string;
  plural: string;
  fields: CrudField[];
  /** Si es true, no se muestra en el menú de administración */
  hidden?: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class EntityConfigService {
  private configs: Record<string, EntityConfig> = {
    clientes: {
      key: 'clientes',
      endpoint: 'clientes',
      label: 'Cliente',
      plural: 'Clientes',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'documento', label: 'Documento', type: 'text', required: true, showInTable: true },
        { key: 'nombreCompleto', label: 'Nombre completo', type: 'text', required: true, showInTable: true },
        { key: 'tipo', label: 'Tipo', type: 'select', required: true, options: [{ label: 'Persona', value: 'PERSONA' }, { label: 'Empresa', value: 'EMPRESA' }, { label: 'Copropiedad', value: 'COPROPIEDAD' }], showInTable: true },
        { key: 'direccion', label: 'Dirección', type: 'text', showInTable: false },
        { key: 'ciudad', label: 'Ciudad', type: 'text', showInTable: true },
        { key: 'departamento', label: 'Departamento', type: 'text', showInTable: false },
        { key: 'telefono', label: 'Teléfono', type: 'text', showInTable: true },
        { key: 'email', label: 'Email', type: 'email', showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'configuracion-empresa': {
      key: 'configuracion-empresa',
      endpoint: 'configuracion-empresa',
      label: 'Configuración de empresa',
      plural: 'Configuraciones de empresa',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'nit', label: 'NIT', type: 'text', required: true, showInTable: true },
        { key: 'direccion', label: 'Dirección', type: 'text', showInTable: false },
        { key: 'telefono', label: 'Teléfono', type: 'text', showInTable: true },
        { key: 'email', label: 'Email', type: 'email', showInTable: true },
        { key: 'sitioWeb', label: 'Sitio web', type: 'text', showInTable: false },
        { key: 'logo', label: 'Logo', type: 'text', showInTable: false },
        { key: 'descripcion', label: 'Descripción', type: 'textarea', showInTable: false },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    contactos: {
      key: 'contactos',
      endpoint: 'contactos',
      label: 'Contacto',
      plural: 'Contactos',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'clienteId', label: 'Cliente', type: 'select', required: true, source: { endpoint: 'clientes', valueField: 'id', labelField: 'nombreCompleto' }, showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'cargo', label: 'Cargo', type: 'text', showInTable: true },
        { key: 'telefono', label: 'Teléfono', type: 'text', showInTable: true },
        { key: 'email', label: 'Email', type: 'email', showInTable: true },
        { key: 'principal', label: 'Principal', type: 'select', required: true, options: [{ label: 'Sí', value: 'S' }, { label: 'No', value: 'N' }], showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'planes-ppa': {
      key: 'planes-ppa',
      endpoint: 'planes-ppa',
      label: 'Plan PPA',
      plural: 'Planes PPA',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'proyectoId', label: 'Proyecto', type: 'select', required: true, source: { endpoint: 'proyectos', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'duracionAnos', label: 'Duración (años)', type: 'number', required: true, showInTable: true },
        { key: 'descuento', label: 'Descuento', type: 'number', required: true, showInTable: true },
        { key: 'inversion', label: 'Inversión', type: 'number', showInTable: false },
        { key: 'incluyeOM', label: 'Incluye O&M', type: 'select', required: true, options: [{ label: 'Sí', value: 'S' }, { label: 'No', value: 'N' }], showInTable: true },
        { key: 'incluyeRetie', label: 'Incluye RETIE', type: 'select', required: true, options: [{ label: 'Sí', value: 'S' }, { label: 'No', value: 'N' }], showInTable: true },
        { key: 'incluyeLegalizacion', label: 'Incluye legalización', type: 'select', required: true, options: [{ label: 'Sí', value: 'S' }, { label: 'No', value: 'N' }], showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'plantillas-documento': {
      key: 'plantillas-documento',
      endpoint: 'plantillas-documento',
      label: 'Plantilla de documento',
      plural: 'Plantillas de documento',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'codigo', label: 'Código', type: 'text', required: true, showInTable: true },
        { key: 'version', label: 'Versión', type: 'text', required: true, showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'plantilla-secciones': {
      key: 'plantilla-secciones',
      endpoint: 'plantilla-secciones',
      label: 'Sección de plantilla',
      plural: 'Secciones de plantilla',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'plantillaId', label: 'Plantilla', type: 'select', required: true, source: { endpoint: 'plantillas-documento', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'codigo', label: 'Código', type: 'text', required: true, showInTable: true },
        { key: 'titulo', label: 'Título', type: 'text', required: true, showInTable: true },
        { key: 'orden', label: 'Orden', type: 'number', required: true, showInTable: true },
        { key: 'contenido', label: 'Contenido', type: 'textarea', showInTable: false },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'plantilla-variables': {
      key: 'plantilla-variables',
      endpoint: 'plantilla-variables',
      label: 'Variable de plantilla',
      plural: 'Variables de plantilla',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'seccionId', label: 'Sección', type: 'select', required: true, source: { endpoint: 'plantilla-secciones', valueField: 'id', labelField: 'titulo' }, showInTable: true },
        { key: 'codigo', label: 'Código', type: 'text', required: true, showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'tipo', label: 'Tipo', type: 'select', required: true, options: [{ label: 'Texto', value: 'TEXTO' }, { label: 'Número', value: 'NUMERO' }, { label: 'Moneda', value: 'MONEDA' }, { label: 'Fecha', value: 'FECHA' }, { label: 'Booleano', value: 'BOOLEANO' }], showInTable: true },
        { key: 'formato', label: 'Formato', type: 'text', showInTable: false }
      ]
    },
    proyectos: {
      key: 'proyectos',
      endpoint: 'proyectos',
      label: 'Proyecto',
      plural: 'Proyectos',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'clienteId', label: 'Cliente', type: 'select', required: true, source: { endpoint: 'clientes', valueField: 'id', labelField: 'nombreCompleto' }, showInTable: true },
        { key: 'sedeId', label: 'Sede', type: 'select', source: { endpoint: 'sedes', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'visitaId', label: 'Visita', type: 'select', source: { endpoint: 'visitas', valueField: 'id', labelField: 'observaciones' }, showInTable: false },
        { key: 'codigo', label: 'Código', type: 'text', required: true, showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'estado', label: 'Estado comercial', type: 'select', required: true, source: { endpoint: 'proyectos/estados', valueField: 'value', labelField: 'label' }, showInTable: true }
      ]
    },
    roles: {
      key: 'roles',
      endpoint: 'roles',
      label: 'Rol',
      plural: 'Roles',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'descripcion', label: 'Descripción', type: 'textarea', showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    sedes: {
      key: 'sedes',
      endpoint: 'sedes',
      label: 'Sede',
      plural: 'Sedes',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'clienteId', label: 'Cliente', type: 'select', required: true, source: { endpoint: 'clientes', valueField: 'id', labelField: 'nombreCompleto' }, showInTable: true },
        { key: 'nombre', label: 'Nombre', type: 'text', required: true, showInTable: true },
        { key: 'direccion', label: 'Dirección', type: 'text', showInTable: false },
        { key: 'ciudad', label: 'Ciudad', type: 'text', showInTable: true },
        { key: 'departamento', label: 'Departamento', type: 'text', showInTable: false },
        { key: 'area', label: 'Área (m²)', type: 'number', showInTable: true },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    sesiones: {
      key: 'sesiones',
      endpoint: 'sesiones',
      label: 'Sesión',
      plural: 'Sesiones',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'usuarioId', label: 'Usuario', type: 'select', required: true, source: { endpoint: 'usuarios', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'token', label: 'Token', type: 'textarea', showInTable: false },
        { key: 'fechaExpiracion', label: 'Fecha expiración', type: 'datetime-local', showInTable: false },
        { key: 'fechaUltimoUso', label: 'Último uso', type: 'datetime-local', showInTable: false },
        { key: 'ip', label: 'IP', type: 'text', showInTable: true },
        { key: 'dispositivo', label: 'Dispositivo', type: 'text', showInTable: false },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'textos-parametrizables': {
      key: 'textos-parametrizables',
      endpoint: 'textos-parametrizables',
      label: 'Texto parametrizable',
      plural: 'Textos parametrizables',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'codigo', label: 'Código', type: 'text', required: true, showInTable: true },
        { key: 'titulo', label: 'Título', type: 'text', required: true, showInTable: true },
        { key: 'contenido', label: 'Contenido', type: 'textarea', showInTable: false },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Activo', value: 'A' }, { label: 'Inactivo', value: 'I' }], showInTable: true }
      ]
    },
    'usuario-roles': {
      key: 'usuario-roles',
      endpoint: 'usuario-roles', // Homologado a 'usuario-roles'
      label: 'Asignación usuario-rol',
      plural: 'Asignaciones usuario-rol',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'usuarioId', label: 'Usuario', type: 'select', required: true, source: { endpoint: 'usuarios', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'rolId', label: 'Rol', type: 'select', required: true, source: { endpoint: 'roles', valueField: 'id', labelField: 'nombre' }, showInTable: true }
      ]
    },
    'variables-plantilla': {
      key: 'variables-plantilla',
      endpoint: 'variables-plantilla',
      label: 'Variable de proyección',
      plural: 'Variables de proyección',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'planPpaId', label: 'Plan PPA', type: 'select', required: true, source: { endpoint: 'planes-ppa', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'anoProyeccion', label: 'Año proyección', type: 'number', required: true, showInTable: true },
        { key: 'tarifaConvencional', label: 'Tarifa convencional', type: 'number', required: true, showInTable: true },
        { key: 'consumoEnergia', label: 'Consumo energía', type: 'number', required: true, showInTable: true },
        { key: 'generacionEnergia', label: 'Generación energía', type: 'number', required: true, showInTable: true },
        { key: 'costoRedRemanente', label: 'Costo red remanente', type: 'number', required: true, showInTable: true }
      ]
    },
    'detalle-variables-plantilla': {
      key: 'detalle-variables-plantilla',
      endpoint: 'dtlle-Variables-plantilla',
      label: 'Detalle de variable',
      plural: 'Detalles de variables',
      hidden: true,
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'vplCont', label: 'Variable plantilla', type: 'select', required: true, source: { endpoint: 'variables-plantilla', valueField: 'id', labelField: 'id' }, showInTable: true },
        { key: 'anoProyeccion', label: 'Año proyección', type: 'number', required: true, showInTable: true },
        { key: 'tarifaConvencional', label: 'Tarifa convencional', type: 'number', showInTable: true },
        { key: 'tarifaPpa', label: 'Tarifa PPA', type: 'number', showInTable: true },
        { key: 'consumoEnergia', label: 'Consumo energía', type: 'number', showInTable: true },
        { key: 'generacionEnergia', label: 'Generación energía', type: 'number', showInTable: true },
        { key: 'costoConsumoSinSsfv', label: 'Costo consumo sin SSFV', type: 'number', showInTable: false },
        { key: 'costoRedRemanente', label: 'Costo red remanente', type: 'number', showInTable: false },
        { key: 'costoSsfvPpa', label: 'Costo SSFV PPA', type: 'number', showInTable: false },
        { key: 'costoSsfvCostoRed', label: 'Costo SSFV + red', type: 'number', showInTable: false },
        { key: 'ahorroMillones', label: 'Ahorro (millones)', type: 'number', showInTable: true }
      ]
    },
    visitas: {
      key: 'visitas',
      endpoint: 'visitas',
      label: 'Visita técnica',
      plural: 'Visitas técnicas',
      fields: [
        { key: 'id', label: 'ID', type: 'number', showInTable: true },
        { key: 'clienteId', label: 'Cliente', type: 'select', required: true, source: { endpoint: 'clientes', valueField: 'id', labelField: 'nombreCompleto' }, showInTable: true },
        { key: 'sedeId', label: 'Sede', type: 'select', source: { endpoint: 'sedes', valueField: 'id', labelField: 'nombre' }, showInTable: false },
        { key: 'usuarioId', label: 'Técnico', type: 'select', required: true, source: { endpoint: 'usuarios', valueField: 'id', labelField: 'nombre' }, showInTable: true },
        { key: 'fecha', label: 'Fecha', type: 'datetime-local', required: true, showInTable: true },
        { key: 'observaciones', label: 'Observaciones', type: 'textarea', showInTable: false },
        { key: 'estado', label: 'Estado', type: 'select', required: true, options: [{ label: 'Pendiente', value: 'P' }, { label: 'Realizada', value: 'R' }, { label: 'Cancelada', value: 'C' }], showInTable: true }
      ]
    }
  };

  getAll(): EntityConfig[] {
    return Object.values(this.configs);
  }

  get(key: string): EntityConfig | undefined {
    return this.configs[key];
  }
}