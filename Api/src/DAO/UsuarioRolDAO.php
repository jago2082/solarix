<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\UsuarioRolTO;
use PDO;

class UsuarioRolDAO {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias para ocultar los campos de base de datos
        $sql = "SELECT 
                    lInUro_cont AS id,
                    lInUsu_cont AS usuarioId,
                    lInRol_cont AS rolId,
                    lDtUro_fasi AS fechaAsignacion
                FROM usuario_roles";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        $sql = "SELECT 
                    lInUro_cont AS id,
                    lInUsu_cont AS usuarioId,
                    lInRol_cont AS rolId,
                    lDtUro_fasi AS fechaAsignacion
                FROM usuario_roles 
                WHERE lInUro_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(UsuarioRolTO $usuarioRol) {
        $sql = "INSERT INTO usuario_roles (lInUsu_cont, lInRol_cont) 
                VALUES (:usuarioId, :rolId)";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':usuarioId', $usuarioRol->getUsuarioId());
            $stmt->bindValue(':rolId', $usuarioRol->getRolId());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Rol asignado al usuario exitosamente'];
        } catch (\PDOException $e) {
            // Manejamos la violación de la clave única (UNIQUE KEY)
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Este usuario ya tiene asignado este rol.'];
            }
            return ['status' => 'error', 'message' => 'Error al asignar el rol: ' . $e->getMessage()];
        }
    }

    public function update(UsuarioRolTO $usuarioRol, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($usuarioRol->getUsuarioId() !== null) {
            $campos[] = 'lInUsu_cont = :usuarioId';
            $parametros[':usuarioId'] = $usuarioRol->getUsuarioId();
        }
        if ($usuarioRol->getRolId() !== null) {
            $campos[] = 'lInRol_cont = :rolId';
            $parametros[':rolId'] = $usuarioRol->getRolId();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE usuario_roles SET " . implode(', ', $campos) . " WHERE lInUro_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Asignación actualizada exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'La actualización genera una asignación duplicada.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar la asignación.'];
        }
    }

    public function delete($id) {
        // En esta tabla intermedia SÍ hacemos borrado físico, ya que no hay campo de estado
        $sql = "DELETE FROM usuario_roles WHERE lInUro_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Asignación de rol eliminada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La asignación no existe'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar la asignación.'];
        }
    }
}