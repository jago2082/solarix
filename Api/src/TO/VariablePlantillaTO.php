<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="VariablePlantillaTO",
 *     type="object",
 *     title="VariablePlantillaTO",
 *     description="Objeto de Transferencia de Variables de Proyección de Plantilla (PPA)"
 * )
 */
class VariablePlantillaTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del registro de variable")
     */
    private $id;

    /**
     * @OA\Property(property="planPpaId", type="integer", example=1, description="ID del plan PPA al que pertenece la proyección")
     */
    private $planPpaId;

    /**
     * @OA\Property(property="anoProyeccion", type="integer", example=1, description="Año numérico de la proyección (ej. 1, 2, ..., 15)")
     */
    private $anoProyeccion;

    /**
     * @OA\Property(property="tarifaConvencional", type="number", format="float", example=650.50, description="Tarifa de energía de la red convencional ($/kWh)")
     */
    private $tarifaConvencional;

    /**
     * @OA\Property(property="consumoEnergia", type="number", format="float", example=120000.00, description="Consumo estimado de energía (kWh)")
     */
    private $consumoEnergia;

    /**
     * @OA\Property(property="generacionEnergia", type="number", format="float", example=95000.00, description="Generación solar estimada (kWh)")
     */
    private $generacionEnergia;

    /**
     * @OA\Property(property="costoRedRemanente", type="number", format="float", example=16262500.00, description="Costo de la energía remanente tomada de la red ($)")
     */
    private $costoRedRemanente;

    /**
     * @OA\Property(property="fechaCreacion", type="string", format="date-time", example="2025-01-01T00:00:00", description="Fecha y hora de creación del registro")
     */
    private $fechaCreacion;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getPlanPpaId() { return $this->planPpaId; }
    public function setPlanPpaId($planPpaId) { $this->planPpaId = $planPpaId; }

    public function getAnoProyeccion() { return $this->anoProyeccion; }
    public function setAnoProyeccion($anoProyeccion) { $this->anoProyeccion = $anoProyeccion; }

    public function getTarifaConvencional() { return $this->tarifaConvencional; }
    public function setTarifaConvencional($tarifaConvencional) { $this->tarifaConvencional = $tarifaConvencional; }

    public function getConsumoEnergia() { return $this->consumoEnergia; }
    public function setConsumoEnergia($consumoEnergia) { $this->consumoEnergia = $consumoEnergia; }

    public function getGeneracionEnergia() { return $this->generacionEnergia; }
    public function setGeneracionEnergia($generacionEnergia) { $this->generacionEnergia = $generacionEnergia; }

    public function getCostoRedRemanente() { return $this->costoRedRemanente; }
    public function setCostoRedRemanente($costoRedRemanente) { $this->costoRedRemanente = $costoRedRemanente; }

    public function getFechaCreacion() { return $this->fechaCreacion; }
    public function setFechaCreacion($fechaCreacion) { $this->fechaCreacion = $fechaCreacion; }
}
