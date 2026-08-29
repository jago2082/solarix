<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="PlantillaVariableTO",
 *     type="object",
 *     title="PlantillaVariableTO",
 *     description="Objeto de Transferencia de Variable de Plantilla"
 * )
 */
class PlantillaVariableTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la variable de plantilla")
     */
    private $id;

    /**
     * @OA\Property(property="seccionId", type="integer", example=1, description="ID de la sección de plantilla a la que pertenece la variable")
     */
    private $seccionId;

    /**
     * @OA\Property(property="codigo", type="string", example="VAR_POTENCIA_KW", description="Código de la variable usado como marcador de posición (placeholder)")
     */
    private $codigo;

    /**
     * @OA\Property(property="nombre", type="string", example="Potencia Instalada (kWp)", description="Nombre descriptivo de la variable")
     */
    private $nombre;

    /**
     * @OA\Property(property="tipo", type="string", enum={"TEXTO", "NUMERO", "MONEDA", "FECHA", "BOOLEANO"}, example="NUMERO", description="Tipo de dato de la variable")
     */
    private $tipo;

    /**
     * @OA\Property(property="formato", type="string", example="#,##0.00", nullable=true, description="Formato de visualización o máscara del valor (opcional)")
     */
    private $formato;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getSeccionId() { return $this->seccionId; }
    public function setSeccionId($seccionId) { $this->seccionId = $seccionId; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getTipo() { return $this->tipo; }
    public function setTipo($tipo) { $this->tipo = $tipo; }

    public function getFormato() { return $this->formato; }
    public function setFormato($formato) { $this->formato = $formato; }
}