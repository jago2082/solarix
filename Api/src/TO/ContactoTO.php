<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="ContactoTO",
 *     type="object",
 *     title="ContactoTO",
 *     description="Objeto de Transferencia de Contacto del Cliente"
 * )
 */
class ContactoTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del contacto")
     */
    private $id;

    /**
     * @OA\Property(property="clienteId", type="integer", example=1, description="ID del cliente al que pertenece el contacto")
     */
    private $clienteId;

    /**
     * @OA\Property(property="nombre", type="string", example="María Fernanda Gómez", description="Nombre completo del contacto")
     */
    private $nombre;

    /**
     * @OA\Property(property="cargo", type="string", example="Gerente de Operaciones", nullable=true, description="Cargo del contacto en la empresa")
     */
    private $cargo;

    /**
     * @OA\Property(property="telefono", type="string", example="3109876543", nullable=true, description="Teléfono móvil o directo")
     */
    private $telefono;

    /**
     * @OA\Property(property="email", type="string", example="maria.gomez@empresa.com", nullable=true, description="Correo electrónico del contacto")
     */
    private $email;

    /**
     * @OA\Property(property="principal", type="string", enum={"S", "N"}, example="S", description="Indica si es el contacto principal: S = Sí, N = No")
     */
    private $principal;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del contacto: A = Activo, I = Inactivo")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getClienteId() { return $this->clienteId; }
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getCargo() { return $this->cargo; }
    public function setCargo($cargo) { $this->cargo = $cargo; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPrincipal() { return $this->principal; }
    public function setPrincipal($principal) { $this->principal = $principal; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}