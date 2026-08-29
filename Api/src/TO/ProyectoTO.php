<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="ProyectoTO",
 *     type="object",
 *     title="ProyectoTO",
 *     description="Objeto de Transferencia de Proyecto"
 * )
 */
class ProyectoTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del proyecto")
     */
    private $id;

    /**
     * @OA\Property(property="clienteId", type="integer", example=1, description="ID del cliente asociado al proyecto")
     */
    private $clienteId;

    /**
     * @OA\Property(property="sedeId", type="integer", example=1, nullable=true, description="ID de la sede asociada al proyecto")
     */
    private $sedeId;

    /**
     * @OA\Property(property="visitaId", type="integer", example=1, nullable=true, description="ID de la visita técnica asociada")
     */
    private $visitaId;

    /**
     * @OA\Property(property="codigo", type="string", example="PRY-2026-001", description="Código único de identificación del proyecto")
     */
    private $codigo;

    /**
     * @OA\Property(property="nombre", type="string", example="Instalación Planta Solar Fotovoltaica 100kWp", description="Nombre descriptivo del proyecto")
     */
    private $nombre;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del proyecto: A = Activo, I = Inactivo, F = Finalizado")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getClienteId() { return $this->clienteId; }
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }

    public function getSedeId() { return $this->sedeId; }
    public function setSedeId($sedeId) { $this->sedeId = $sedeId; }

    public function getVisitaId() { return $this->visitaId; }
    public function setVisitaId($visitaId) { $this->visitaId = $visitaId; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}