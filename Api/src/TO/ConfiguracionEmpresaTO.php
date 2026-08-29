<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="ConfiguracionEmpresaTO",
 *     type="object",
 *     title="ConfiguracionEmpresaTO",
 *     description="Objeto de Transferencia de Configuración de Empresa"
 * )
 */
class ConfiguracionEmpresaTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la configuración de empresa")
     */
    private $id;

    /**
     * @OA\Property(property="nombre", type="string", example="Empresa Ejemplo S.A.S.", description="Nombre comercial o razón social de la empresa")
     */
    private $nombre;

    /**
     * @OA\Property(property="nit", type="string", example="900987654-3", description="Número de Identificación Tributaria (NIT)")
     */
    private $nit;

    /**
     * @OA\Property(property="direccion", type="string", example="Carrera 7 # 71-21", nullable=true, description="Dirección principal de la empresa")
     */
    private $direccion;

    /**
     * @OA\Property(property="telefono", type="string", example="6011234567", nullable=true, description="Teléfono principal de contacto")
     */
    private $telefono;

    /**
     * @OA\Property(property="email", type="string", example="info@empresa.com", nullable=true, description="Correo electrónico corporativo")
     */
    private $email;

    /**
     * @OA\Property(property="sitioWeb", type="string", example="https://www.empresa.com", nullable=true, description="URL del sitio web de la empresa")
     */
    private $sitioWeb;

    /**
     * @OA\Property(property="logo", type="string", example="logo_empresa.png", nullable=true, description="Ruta o URL de la imagen del logotipo")
     */
    private $logo;

    /**
     * @OA\Property(property="descripcion", type="string", example="Empresa dedicada a soluciones energéticas integrales", nullable=true, description="Descripción o reseña corta de la empresa")
     */
    private $descripcion;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado de la empresa: A = Activa, I = Inactiva")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getNit() { return $this->nit; }
    public function setNit($nit) { $this->nit = $nit; }

    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getSitioWeb() { return $this->sitioWeb; }
    public function setSitioWeb($sitioWeb) { $this->sitioWeb = $sitioWeb; }

    public function getLogo() { return $this->logo; }
    public function setLogo($logo) { $this->logo = $logo; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}