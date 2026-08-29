<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="SesionTO",
 *     type="object",
 *     title="SesionTO",
 *     description="Objeto de Transferencia de Sesión de Usuario"
 * )
 */
class SesionTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único de la sesión")
     */
    private $id;

    /**
     * @OA\Property(property="usuarioId", type="integer", example=1, description="ID del usuario autenticado")
     */
    private $usuarioId;

    /**
     * @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...", description="Token de autenticación (JWT o Hash)")
     */
    private $token;

    /**
     * @OA\Property(property="fechaExpiracion", type="string", format="date-time", example="2026-08-28T23:59:59Z", nullable=true, description="Fecha y hora de expiración del token")
     */
    private $fechaExpiracion;

    /**
     * @OA\Property(property="fechaUltimoUso", type="string", format="date-time", example="2026-08-27T21:30:00Z", nullable=true, description="Fecha y hora de la última interacción del usuario")
     */
    private $fechaUltimoUso;

    /**
     * @OA\Property(property="ip", type="string", example="192.168.1.50", nullable=true, description="Dirección IP del cliente")
     */
    private $ip;

    /**
     * @OA\Property(property="dispositivo", type="string", example="Chrome on Windows / Ionic App Android", nullable=true, description="Información del dispositivo o navegador (User Agent)")
     */
    private $dispositivo;

    /**
     * @OA\Property(property="estado", type="string", example="A", description="Estado de la sesión: A = Activa, I = Inactiva / Expirada")
     */
    private $estado;

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getUsuarioId() { return $this->usuarioId; }
    public function setUsuarioId($usuarioId) { $this->usuarioId = $usuarioId; }

    public function getToken() { return $this->token; }
    public function setToken($token) { $this->token = $token; }

    public function getFechaExpiracion() { return $this->fechaExpiracion; }
    public function setFechaExpiracion($fechaExpiracion) { $this->fechaExpiracion = $fechaExpiracion; }

    public function getFechaUltimoUso() { return $this->fechaUltimoUso; }
    public function setFechaUltimoUso($fechaUltimoUso) { $this->fechaUltimoUso = $fechaUltimoUso; }

    public function getIp() { return $this->ip; }
    public function setIp($ip) { $this->ip = $ip; }

    public function getDispositivo() { return $this->dispositivo; }
    public function setDispositivo($dispositivo) { $this->dispositivo = $dispositivo; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}