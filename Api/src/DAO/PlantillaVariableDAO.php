<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\PlantillaVariableTO;
use PDO;

class PlantillaVariableDAO {
    private $conn;

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT 
                    lInPva_cont AS id,
                    lInPse_cont AS seccionId,
                    lStPva_codi AS codigo,
                    lStPva_nomb AS nombre,
                    lStPva_tipo AS tipo,
                    lStPva_form AS formato
                FROM plantilla_variables";
                
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
                    lInPva_cont AS id,
                    lInPse_cont AS seccionId,
                    lStPva_codi AS codigo,
                    lStPva_nomb AS nombre,
                    lStPva_tipo AS tipo,
                    lStPva_form AS formato
                FROM plantilla_variables 
                WHERE lInPva_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getBySeccionId($seccionId) {
        $sql = "SELECT 
                    lInPva_cont AS id,
                    lInPse_cont AS seccionId,
                    lStPva_codi AS codigo,
                    lStPva_nomb AS nombre,
                    lStPva_tipo AS tipo,
                    lStPva_form AS formato
                FROM plantilla_variables 
                WHERE lInPse_cont = :seccionId";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':seccionId', $seccionId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function create(PlantillaVariableTO $variable) {
        $sql = "INSERT INTO plantilla_variables (
                    lInPse_cont, lStPva_codi, lStPva_nomb, 
                    lStPva_tipo, lStPva_form
                ) VALUES (
                    :seccionId, :codigo, :nombre, 
                    :tipo, :formato
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':seccionId', $variable->getSeccionId(), PDO::PARAM_INT);
            $stmt->bindValue(':codigo', $variable->getCodigo());
            $stmt->bindValue(':nombre', $variable->getNombre());
            $stmt->bindValue(':tipo', $variable->getTipo());
            $stmt->bindValue(':formato', $variable->getFormato());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Variable de plantilla registrada exitosamente'];
        } catch (\PDOException $e) {
            // Manejamos la violación del UNIQUE KEY (lInPse_cont, lStPva_codi)
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código ingresado ya existe para esta sección. Por favor asigne uno diferente.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar la variable.'];
        }
    }

    public function update(PlantillaVariableTO $variable, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($variable->getSeccionId() !== null) {
            $campos[] = 'lInPse_cont = :seccionId';
            $parametros[':seccionId'] = $variable->getSeccionId();
        }
        if ($variable->getCodigo() !== null) {
            $campos[] = 'lStPva_codi = :codigo';
            $parametros[':codigo'] = $variable->getCodigo();
        }
        if ($variable->getNombre() !== null) {
            $campos[] = 'lStPva_nomb = :nombre';
            $parametros[':nombre'] = $variable->getNombre();
        }
        if ($variable->getTipo() !== null) {
            $campos[] = 'lStPva_tipo = :tipo';
            $parametros[':tipo'] = $variable->getTipo();
        }
        if ($variable->getFormato() !== null) {
            $campos[] = 'lStPva_form = :formato';
            $parametros[':formato'] = $variable->getFormato();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE plantilla_variables SET " . implode(', ', $campos) . " WHERE lInPva_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Variable actualizada exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código modificado ya pertenece a otra variable en esta sección.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar la variable.'];
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM plantilla_variables WHERE lInPva_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Variable eliminada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La variable no existe'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar la variable.'];
        }
    }
}