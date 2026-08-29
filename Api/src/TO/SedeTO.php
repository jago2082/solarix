<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="SedeTO",
 *     type="object",
 *     title="SedeTO",
 *     description="Objeto de Transferencia de Sede de Cliente"
 * )
 */
class SedeTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la sede")
     */
    private $id;

    /**
     * @OA\Property(property="clienteId", type="integer", example=1, description="ID del cliente al que pertenece la sede")
     */
    private $clienteId;

    /**
     * @OA\Property(property="nombre", type="string", example="Sede Principal Industrial", description="Nombre descriptivo de la sede")
     */
    private $nombre;

    /**
     * @OA\Property(property="direccion", type="string", example="Zona Franca Bodega 12", nullable=true, description="Dirección de la sede")
     */
    private $direccion;

    /**
     * @OA\Property(property="ciudad", type="string", example="Medellín", nullable=true)
     */
    private $ciudad;

    /**
     * @OA\Property(property="departamento", type="string", example="Antioquia", nullable=true)
     */
    private $departamento;

    /**
     * @OA\Property(property="area", type="number", format="float", example=450.50, nullable=true, description="Área en metros cuadrados de la sede")
     */
    private $area;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado de la sede: A = Activa, I = Inactiva")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getClienteId() { return $this->clienteId; }
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function getCiudad() { return $this->ciudad; }
    public function setCiudad($ciudad) { $this->ciudad = $ciudad; }

    public function getDepartamento() { return $this->departamento; }
    public function setDepartamento($departamento) { $this->departamento = $departamento; }

    public function getArea() { return $this->area; }
    public function setArea($area) { $this->area = $area; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}