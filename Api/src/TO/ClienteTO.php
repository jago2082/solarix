<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="ClienteTO",
 *     type="object",
 *     title="ClienteTO",
 *     description="Objeto de Transferencia de Cliente"
 * )
 */
class ClienteTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del cliente")
     */
    private $id;

    /**
     * @OA\Property(property="documento", type="string", example="900123456-1", nullable=true, description="NIT o documento de identidad")
     */
    private $documento;

    /**
     * @OA\Property(property="nombreCompleto", type="string", example="Empresa Soluciones Solar S.A.S.", description="Nombre completo o razón social")
     */
    private $nombreCompleto;

    /**
     * @OA\Property(property="tipo", type="string", enum={"PERSONA", "EMPRESA", "COPROPIEDAD"}, example="EMPRESA", description="Tipo de cliente")
     */
    private $tipo;

    /**
     * @OA\Property(property="direccion", type="string", example="Calle 100 # 15-20", nullable=true)
     */
    private $direccion;

    /**
     * @OA\Property(property="ciudad", type="string", example="Bogotá", nullable=true)
     */
    private $ciudad;

    /**
     * @OA\Property(property="departamento", type="string", example="Cundinamarca", nullable=true)
     */
    private $departamento;

    /**
     * @OA\Property(property="telefono", type="string", example="6017654321", nullable=true)
     */
    private $telefono;

    /**
     * @OA\Property(property="email", type="string", example="contacto@empresa.com", nullable=true)
     */
    private $email;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del cliente: A = Activo, I = Inactivo")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDocumento() { return $this->documento; }
    public function setDocumento($documento) { $this->documento = $documento; }

    public function getNombreCompleto() { return $this->nombreCompleto; }
    public function setNombreCompleto($nombreCompleto) { $this->nombreCompleto = $nombreCompleto; }

    public function getTipo() { return $this->tipo; }
    public function setTipo($tipo) { $this->tipo = $tipo; }

    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function getCiudad() { return $this->ciudad; }
    public function setCiudad($ciudad) { $this->ciudad = $ciudad; }

    public function getDepartamento() { return $this->departamento; }
    public function setDepartamento($departamento) { $this->departamento = $departamento; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}