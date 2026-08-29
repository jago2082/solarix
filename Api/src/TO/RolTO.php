<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="RolTO",
 *     type="object",
 *     title="RolTO",
 *     description="Objeto de Transferencia de Rol de Usuario"
 * )
 */
class RolTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del rol")
     */
    private $id;

    /**
     * @OA\Property(property="nombre", type="string", example="ADMINISTRADOR", description="Nombre del rol del sistema")
     */
    private $nombre;

    /**
     * @OA\Property(property="descripcion", type="string", example="Rol con acceso total al sistema", nullable=true, description="Descripción corta de las responsabilidades o permisos del rol")
     */
    private $descripcion;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del rol: A = Activo, I = Inactivo")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}