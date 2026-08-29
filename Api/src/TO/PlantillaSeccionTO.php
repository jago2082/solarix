<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="PlantillaSeccionTO",
 *     type="object",
 *     title="PlantillaSeccionTO",
 *     description="Objeto de Transferencia de Sección de Plantilla"
 * )
 */
class PlantillaSeccionTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la sección")
     */
    private $id;

    /**
     * @OA\Property(property="plantillaId", type="integer", example=1, description="ID de la plantilla de documento a la que pertenece la sección")
     */
    private $plantillaId;

    /**
     * @OA\Property(property="codigo", type="string", example="SEC-RESUMEN-EJEC", description="Código de identificación de la sección")
     */
    private $codigo;

    /**
     * @OA\Property(property="titulo", type="string", example="Resumen Ejecutivo del Proyecto", description="Título de la sección")
     */
    private $titulo;

    /**
     * @OA\Property(property="orden", type="integer", example=1, description="Orden numérico de aparición en el documento")
     */
    private $orden;

    /**
     * @OA\Property(property="contenido", type="string", example="<p>El presente proyecto contempla la instalación de un sistema fotovoltaico...</p>", nullable=true, description="Contenido en texto o formato HTML")
     */
    private $contenido;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado de la sección: A = Activa, I = Inactiva")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getPlantillaId() { return $this->plantillaId; }
    public function setPlantillaId($plantillaId) { $this->plantillaId = $plantillaId; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getTitulo() { return $this->titulo; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }

    public function getOrden() { return $this->orden; }
    public function setOrden($orden) { $this->orden = $orden; }

    public function getContenido() { return $this->contenido; }
    public function setContenido($contenido) { $this->contenido = $contenido; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}