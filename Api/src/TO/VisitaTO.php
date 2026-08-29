<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="VisitaTO",
 *     type="object",
 *     title="VisitaTO",
 *     description="Objeto de Transferencia de Visita Técnica"
 * )
 */
class VisitaTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la visita")
     */
    private $id;

    /**
     * @OA\Property(property="clienteId", type="integer", example=1, description="ID del cliente asociado a la visita")
     */
    private $clienteId;

    /**
     * @OA\Property(property="sedeId", type="integer", example=1, nullable=true, description="ID de la sede visitada")
     */
    private $sedeId;

    /**
     * @OA\Property(property="usuarioId", type="integer", example=1, description="ID del usuario/técnico que realiza la visita")
     */
    private $usuarioId;

    /**
     * @OA\Property(property="fecha", type="string", format="date-time", example="2026-08-27T10:00:00Z", description="Fecha y hora de la visita")
     */
    private $fecha;

    /**
     * @OA\Property(property="observaciones", type="string", example="Inspección de cubierta realizada. Se verifica área disponible para paneles.", nullable=true, description="Observaciones o notas de la visita")
     */
    private $observaciones;

    /**
     * @OA\Property(property="estado", type="string", example="P", description="Estado de la visita: P = Pendiente, R = Realizada, C = Cancelada")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getClienteId() { return $this->clienteId; }
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }

    public function getSedeId() { return $this->sedeId; }
    public function setSedeId($sedeId) { $this->sedeId = $sedeId; }

    public function getUsuarioId() { return $this->usuarioId; }
    public function setUsuarioId($usuarioId) { $this->usuarioId = $usuarioId; }

    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    public function getObservaciones() { return $this->observaciones; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}