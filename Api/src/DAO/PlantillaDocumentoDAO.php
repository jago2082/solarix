<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\PlantillaDocumentoTO;
use PDO;

class PlantillaDocumentoDAO {
    private $conn;  

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Traemos solo las plantillas activas.
        $sql = "SELECT 
                    lInPtd_cont AS id,
                    lStPtd_nomb AS nombre,
                    lStPtd_codi AS codigo,
                    lStPtd_vers AS version,
                    lStPtd_esta AS estado,
                    lStPtd_fecr AS fechaCreacion
                FROM plantillas_documento 
                WHERE lStPtd_esta = 'A'";
                
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
                    lInPtd_cont AS id,
                    lStPtd_nomb AS nombre,
                    lStPtd_codi AS codigo,
                    lStPtd_vers AS version,
                    lStPtd_esta AS estado,
                    lStPtd_fecr AS fechaCreacion
                FROM plantillas_documento 
                WHERE lInPtd_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getActiva() {
        $sql = "SELECT 
                    lInPtd_cont AS id,
                    lStPtd_nomb AS nombre,
                    lStPtd_codi AS codigo,
                    lStPtd_vers AS version,
                    lStPtd_esta AS estado,
                    lStPtd_fecr AS fechaCreacion
                FROM plantillas_documento 
                WHERE lStPtd_esta = 'A'
                ORDER BY lInPtd_cont ASC
                LIMIT 1";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(PlantillaDocumentoTO $plantilla) {
        $sql = "INSERT INTO plantillas_documento (
                    lStPtd_nomb, lStPtd_codi, lStPtd_vers, lStPtd_esta
                ) VALUES (
                    :nombre, :codigo, :version, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', $plantilla->getNombre());
            $stmt->bindValue(':codigo', $plantilla->getCodigo());
            $stmt->bindValue(':version', $plantilla->getVersion());
            $stmt->bindValue(':estado', $plantilla->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Plantilla de documento registrada exitosamente'];
        } catch (\PDOException $e) {
            // Manejo de la violación del campo UNIQUE lStPtd_codi
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código de la plantilla ya existe. Por favor ingrese uno diferente.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar la plantilla de documento.'];
        }
    }

    public function update(PlantillaDocumentoTO $plantilla, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($plantilla->getNombre() !== null) {
            $campos[] = 'lStPtd_nomb = :nombre';
            $parametros[':nombre'] = $plantilla->getNombre();
        }
        if ($plantilla->getCodigo() !== null) {
            $campos[] = 'lStPtd_codi = :codigo';
            $parametros[':codigo'] = $plantilla->getCodigo();
        }
        if ($plantilla->getVersion() !== null) {
            $campos[] = 'lStPtd_vers = :version';
            $parametros[':version'] = $plantilla->getVersion();
        }
        if ($plantilla->getEstado() !== null) {
            $campos[] = 'lStPtd_esta = :estado';
            $parametros[':estado'] = $plantilla->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE plantillas_documento SET " . implode(', ', $campos) . " WHERE lInPtd_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Plantilla de documento actualizada exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código ingresado ya pertenece a otra plantilla.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar la plantilla de documento.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado a 'I' (Inactivo)
        $sql = "UPDATE plantillas_documento SET lStPtd_esta = 'I' WHERE lInPtd_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Plantilla de documento desactivada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La plantilla no existe o ya estaba inactiva'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar la plantilla de documento.'];
        }
    }
}