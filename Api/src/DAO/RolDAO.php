<?php
namespace App\DAO;

use App\Config\Database;
use App\TO\RolTO;
use PDO;
use PDOException;

class RolDAO {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT 
                    lInRol_cont AS id,
                    lStRol_nomb AS nombre,
                    lStRol_desc AS descripcion,
                    lStRol_esta AS estado,
                    lDtRol_fecr AS fechaCreacion
                FROM roles 
                WHERE lStRol_esta = 'A'";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        $sql = "SELECT 
                    lInRol_cont AS id,
                    lStRol_nomb AS nombre,
                    lStRol_desc AS descripcion,
                    lStRol_esta AS estado,
                    lDtRol_fecr AS fechaCreacion
                FROM roles 
                WHERE lInRol_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create(RolTO $rol) {
        $sql = "INSERT INTO roles (lStRol_nomb, lStRol_desc, lStRol_esta) 
                VALUES (:nombre, :descripcion, :estado)";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', $rol->getNombre());
            $stmt->bindValue(':descripcion', $rol->getDescripcion());
            $stmt->bindValue(':estado', $rol->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Rol creado exitosamente'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al crear el rol, posible nombre duplicado.'];
        }
    }

    public function update(RolTO $rol, $id) {
        $mapaCampos = [
            'lStRol_nomb' => $rol->getNombre(),
            'lStRol_desc' => $rol->getDescripcion(),
            'lStRol_esta' => $rol->getEstado()
        ];

        $campos = [];
        $parametros = [':id' => $id];

        foreach ($mapaCampos as $columna => $valor) {
            if ($valor !== null) {
                $param = ':' . $columna;
                $campos[] = "{$columna} = {$param}";
                $parametros[$param] = $valor;
            }
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE roles SET " . implode(', ', $campos) . " WHERE lInRol_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Rol actualizado exitosamente'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar el rol.'];
        }
    }

    public function delete($id) {
        // Borrado L¨®gico: Pasamos el estado a Inactivo ('I')
        $sql = "UPDATE roles SET lStRol_esta = 'I' WHERE lInRol_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Rol desactivado exitosamente'];
            }
            return ['status' => 'error', 'message' => 'El rol no existe o ya est¨¢ inactivo'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar el rol.'];
        }
    }
}