<?php
namespace App\TO;

/**
 * @OA\Schema(
 *     schema="UsuarioRolTO",
 *     type="object",
 *     title="UsuarioRolTO",
 *     description="Objeto de Transferencia de Relación Usuario - Rol"
 * )
 */
class UsuarioRolTO {
    /**
     * @OA\Property(property="id", type="integer", example=1, nullable=true, description="Identificador único del registro relación usuario-rol")
     */
    private $id;

    /**
     * @OA\Property(property="usuarioId", type="integer", example=1, description="ID del usuario asociado")
     */
    private $usuarioId;

    /**
     * @OA\Property(property="rolId", type="integer", example=2, description="ID del rol asignado")
     */
    private $rolId;

    // Getters y Setters para ID
    public function getId() { 
        return $this->id; 
    }
    public function setId($id) { 
        $this->id = $id; 
    }

    // Getters y Setters para UsuarioId
    public function getUsuarioId() { 
        return $this->usuarioId; 
    }
    public function setUsuarioId($usuarioId) { 
        $this->usuarioId = $usuarioId; 
    }

    // Getters y Setters para RolId
    public function getRolId() { 
        return $this->rolId; 
    }
    public function setRolId($rolId) { 
        $this->rolId = $rolId; 
    }
}