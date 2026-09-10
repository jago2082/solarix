<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="DtlleVariablePlantillaTO",
 *     type="object",
 *     title="DtlleVariablePlantillaTO",
 *     description="Objeto de Transferencia del Detalle de Variables de Proyección de Plantilla (PPA)"
 * )
 */
class DtlleVariablePlantillaTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del detalle")
     */
    private $id;

    /**
     * @OA\Property(property="vplCont", type="integer", example=1, description="ID del encabezado de variables de plantilla")
     */
    private $vplCont;

    /**
     * @OA\Property(property="anoProyeccion", type="integer", example=1, description="Año numérico de la proyección")
     */
    private $anoProyeccion;

    /**
     * @OA\Property(property="tarifaConvencional", type="number", format="float", example=650.50, description="Tarifa convencional ($/kWh)")
     */
    private $tarifaConvencional;

    /**
     * @OA\Property(property="tarifaPpa", type="number", format="float", example=480.00, description="Tarifa Energía PPA SSFV ($/kWh)")
     */
    private $tarifaPpa;

    /**
     * @OA\Property(property="consumoEnergia", type="number", format="float", example=120000.00, description="Consumo Energía (GWh)")
     */
    private $consumoEnergia;

    /**
     * @OA\Property(property="generacionEnergia", type="number", format="float", example=95000.00, description="Generación Energía SSFV-PPA (GWh)")
     */
    private $generacionEnergia;

    /**
     * @OA\Property(property="costoConsumoSinSsfv", type="number", format="float", example=78060000.00, description="Costo Consumo sin SSFV ($ Millones)")
     */
    private $costoConsumoSinSsfv;

    /**
     * @OA\Property(property="costoRedRemanente", type="number", format="float", example=16262500.00, description="Costo red remanente ($ Millones)")
     */
    private $costoRedRemanente;

    /**
     * @OA\Property(property="costoSsfvPpa", type="number", format="float", example=45600000.00, description="Costo SSFV PPA ($ Millones)")
     */
    private $costoSsfvPpa;

    /**
     * @OA\Property(property="costoSsfvCostoRed", type="number", format="float", example=61862500.00, description="Costo SSFV + Costo red ($ Millones)")
     */
    private $costoSsfvCostoRed;

    /**
     * @OA\Property(property="ahorroMillones", type="number", format="float", example=16.19, description="Ahorro ($ Millones)")
     */
    private $ahorroMillones;

    /**
     * @OA\Property(property="fechaCreacion", type="string", format="date-time", example="2025-01-01T00:00:00", description="Fecha y hora de creación del registro")
     */
    private $fechaCreacion;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getVplCont() { return $this->vplCont; }
    public function setVplCont($vplCont) { $this->vplCont = $vplCont; }

    public function getAnoProyeccion() { return $this->anoProyeccion; }
    public function setAnoProyeccion($anoProyeccion) { $this->anoProyeccion = $anoProyeccion; }

    public function getTarifaConvencional() { return $this->tarifaConvencional; }
    public function setTarifaConvencional($tarifaConvencional) { $this->tarifaConvencional = $tarifaConvencional; }

    public function getTarifaPpa() { return $this->tarifaPpa; }
    public function setTarifaPpa($tarifaPpa) { $this->tarifaPpa = $tarifaPpa; }

    public function getConsumoEnergia() { return $this->consumoEnergia; }
    public function setConsumoEnergia($consumoEnergia) { $this->consumoEnergia = $consumoEnergia; }

    public function getGeneracionEnergia() { return $this->generacionEnergia; }
    public function setGeneracionEnergia($generacionEnergia) { $this->generacionEnergia = $generacionEnergia; }

    public function getCostoConsumoSinSsfv() { return $this->costoConsumoSinSsfv; }
    public function setCostoConsumoSinSsfv($costoConsumoSinSsfv) { $this->costoConsumoSinSsfv = $costoConsumoSinSsfv; }

    public function getCostoRedRemanente() { return $this->costoRedRemanente; }
    public function setCostoRedRemanente($costoRedRemanente) { $this->costoRedRemanente = $costoRedRemanente; }

    public function getCostoSsfvPpa() { return $this->costoSsfvPpa; }
    public function setCostoSsfvPpa($costoSsfvPpa) { $this->costoSsfvPpa = $costoSsfvPpa; }

    public function getCostoSsfvCostoRed() { return $this->costoSsfvCostoRed; }
    public function setCostoSsfvCostoRed($costoSsfvCostoRed) { $this->costoSsfvCostoRed = $costoSsfvCostoRed; }

    public function getAhorroMillones() { return $this->ahorroMillones; }
    public function setAhorroMillones($ahorroMillones) { $this->ahorroMillones = $ahorroMillones; }

    public function getFechaCreacion() { return $this->fechaCreacion; }
    public function setFechaCreacion($fechaCreacion) { $this->fechaCreacion = $fechaCreacion; }
}
