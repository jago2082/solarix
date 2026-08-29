<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="TextoParametrizableTO",
 *     type="object",
 *     title="TextoParametrizableTO",
 *     description="Objeto de Transferencia de Texto Parametrizable"
 * )
 */
class TextoParametrizableTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del texto parametrizable")
     */
    private $id;

    /**
     * @OA\Property(property="codigo", type="string", example="TXT_GARANTIA_EQUIPOS", description="Código único de identificación del texto")
     */
    private $codigo;

    /**
     * @OA\Property(property="titulo", type="string", example="Cláusula de Garantía de Equipos", description="Título del texto parametrizable")
     */
    private $titulo;

    /**
     * @OA\Property(property="contenido", type="string", example="Los inversores cuentan con una garantía de fábrica de 10 años...", nullable=true, description="Contenido del texto o plantilla HTML")
     */
    private $contenido;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado del texto: A = Activo, I = Inactivo")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getTitulo() { return $this->titulo; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }

    public function getContenido() { return $this->contenido; }
    public function setContenido($contenido) { $this->contenido = $contenido; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}