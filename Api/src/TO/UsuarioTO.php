<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="UsuarioTO",
 *     type="object",
 *     title="UsuarioTO",
 *     description="Objeto de Transferencia de Usuario del Sistema"
 * )
 */
class UsuarioTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del usuario")
     */
    private $id;

    /**
     * @OA\Property(property="nombres", type="string", example="Juan Carlos", description="Nombres del usuario")
     */
    private $nombres;

    /**
     * @OA\Property(property="apellidos", type="string", example="Pérez Rodríguez", description="Apellidos del usuario")
     */
    private $apellidos;

    /**
     * @OA\Property(property="nombreCompleto", type="string", example="Juan Carlos Pérez Rodríguez", nullable=true, description="Nombre completo consolidado")
     */
    private $nombreCompleto;

    /**
     * @OA\Property(property="codigo", type="string", example="USR-001", nullable=true, description="Código de identificación interno del usuario")
     */
    private $codigo;

    /**
     * @OA\Property(property="email", type="string", example="juan.perez@empresa.com", description="Correo electrónico de acceso")
     */
    private $email;

    /**
     * @OA\Property(property="password", type="string", format="password", example="Secret123*", nullable=true, description="Contraseña del usuario (solo en solicitudes de creación/actualización)")
     */
    private $password;

    /**
     * @OA\Property(property="telefono", type="string", example="3001234567", nullable=true, description="Teléfono de contacto del usuario")
     */
    private $telefono;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del usuario: A = Activo, I = Inactivo")
     */
    private $estado;
    
    public function __construct(array $data = []) {
        if (!empty($data)) {
            // Se usa el operador ?? (Null coalescing) por si algún dato no viene en el arreglo
            $this->id = $data['id'] ?? null;
            $this->nombres = $data['nombres'] ?? null;
            $this->apellidos = $data['apellidos'] ?? null;
            $this->nombreCompleto = $data['nombreCompleto'] ?? null;
            $this->codigo = $data['codigo'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->password = $data['password'] ?? null;
            $this->telefono = $data['telefono'] ?? null;
            $this->estado = $data['estado'] ?? 'A'; // Valor por defecto
        }
    }

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }

    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }

    public function getNombreCompleto() { return $this->nombreCompleto; }
    public function setNombreCompleto($nombreCompleto) { $this->nombreCompleto = $nombreCompleto; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}