<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="PlanPpaTO",
 *     type="object",
 *     title="PlanPpaTO",
 *     description="Objeto de Transferencia de Plan PPA (Power Purchase Agreement)"
 * )
 */
class PlanPpaTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del plan PPA")
     */
    private $id;

    /**
     * @OA\Property(property="proyectoId", type="integer", example=1, description="ID del proyecto al que pertenece el plan")
     */
    private $proyectoId;

    /**
     * @OA\Property(property="nombre", type="string", example="Plan PPA Solar 15 Años", description="Nombre descriptivo del plan PPA")
     */
    private $nombre;

    /**
     * @OA\Property(property="duracionAnos", type="integer", example=15, description="Duración del contrato en años")
     */
    private $duracionAnos;

    /**
     * @OA\Property(property="descuento", type="number", format="float", example=12.50, description="Porcentaje de descuento ofrecido en la tarifa")
     */
    private $descuento;

    /**
     * @OA\Property(property="inversion", type="number", format="float", example=150000000.00, nullable=true, description="Monto total estimado de la inversión")
     */
    private $inversion;

    /**
     * @OA\Property(property="incluyeOM", type="string", enum={"S", "N"}, example="S", description="Incluye Operación y Mantenimiento: S = Sí, N = No")
     */
    private $incluyeOM;

    /**
     * @OA\Property(property="incluyeRetie", type="string", enum={"S", "N"}, example="S", description="Incluye certificación RETIE: S = Sí, N = No")
     */
    private $incluyeRetie;

    /**
     * @OA\Property(property="incluyeLegalizacion", type="string", enum={"S", "N"}, example="S", description="Incluye trámites de legalización: S = Sí, N = No")
     */
    private $incluyeLegalizacion;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del plan: A = Activo, I = Inactivo")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getProyectoId() { return $this->proyectoId; }
    public function setProyectoId($proyectoId) { $this->proyectoId = $proyectoId; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getDuracionAnos() { return $this->duracionAnos; }
    public function setDuracionAnos($duracionAnos) { $this->duracionAnos = $duracionAnos; }

    public function getDescuento() { return $this->descuento; }
    public function setDescuento($descuento) { $this->descuento = $descuento; }

    public function getInversion() { return $this->inversion; }
    public function setInversion($inversion) { $this->inversion = $inversion; }

    public function getIncluyeOM() { return $this->incluyeOM; }
    public function setIncluyeOM($incluyeOM) { $this->incluyeOM = $incluyeOM; }

    public function getIncluyeRetie() { return $this->incluyeRetie; }
    public function setIncluyeRetie($incluyeRetie) { $this->incluyeRetie = $incluyeRetie; }

    public function getIncluyeLegalizacion() { return $this->incluyeLegalizacion; }
    public function setIncluyeLegalizacion($incluyeLegalizacion) { $this->incluyeLegalizacion = $incluyeLegalizacion; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}