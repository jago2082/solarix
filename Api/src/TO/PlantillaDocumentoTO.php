<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="PlantillaDocumentoTO",
 *     type="object",
 *     title="PlantillaDocumentoTO",
 *     description="Objeto de Transferencia de Plantilla de Documento"
 * )
 */
class PlantillaDocumentoTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la plantilla de documento")
     */
    private $id;

    /**
     * @OA\Property(property="nombre", type="string", example="Propuesta Comercial PPA Solar", description="Nombre descriptivo de la plantilla")
     */
    private $nombre;

    /**
     * @OA\Property(property="codigo", type="string", example="PLT-PPA-001", description="Código de identificación interna de la plantilla")
     */
    private $codigo;

    /**
     * @OA\Property(property="version", type="string", example="1.0", description="Versión o revisión actual de la plantilla")
     */
    private $version;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado de la plantilla: A = Activa, I = Inactiva")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getVersion() { return $this->version; }
    public function setVersion($version) { $this->version = $version; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}