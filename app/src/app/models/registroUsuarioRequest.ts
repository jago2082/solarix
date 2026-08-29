export interface RegistroUsuarioRequest {
    id?: string | number;
    nombres: string;
    apellidos: string;
    codigo: string;
    telefono: string;
    email: string;
    password?: string;
    estado?: string;
}
