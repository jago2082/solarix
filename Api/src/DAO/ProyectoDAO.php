<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\ProyectoTO;
use PDO;

class ProyectoDAO {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Traemos proyectos que no estén rechazados.
        $sql = "SELECT 
                    lInPro_cont AS id,
                    lInCli_cont AS clienteId,
                    lInSed_cont AS sedeId,
                    lInVis_cont AS visitaId,
                    lStPro_codi AS codigo,
                    lStPro_nomb AS nombre,
                    lStPro_esta AS estado,
                    lDtPro_fech AS fechaCreacion
                FROM proyectos 
                WHERE lStPro_esta != 'RECHAZADO'";
                
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
                    lInPro_cont AS id,
                    lInCli_cont AS clienteId,
                    lInSed_cont AS sedeId,
                    lInVis_cont AS visitaId,
                    lStPro_codi AS codigo,
                    lStPro_nomb AS nombre,
                    lStPro_esta AS estado,
                    lDtPro_fech AS fechaCreacion
                FROM proyectos 
                WHERE lInPro_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(ProyectoTO $proyecto) {
        $sql = "INSERT INTO proyectos (
                    lInCli_cont, lInSed_cont, lInVis_cont, 
                    lStPro_codi, lStPro_nomb, lStPro_esta
                ) VALUES (
                    :clienteId, :sedeId, :visitaId, 
                    :codigo, :nombre, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':clienteId', $proyecto->getClienteId());
            $stmt->bindValue(':sedeId', $proyecto->getSedeId());
            $stmt->bindValue(':visitaId', $proyecto->getVisitaId());
            $stmt->bindValue(':codigo', $proyecto->getCodigo());
            $stmt->bindValue(':nombre', $proyecto->getNombre());
            $stmt->bindValue(':estado', $proyecto->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Proyecto registrado exitosamente'];
        } catch (\PDOException $e) {
            // Manejamos la violación del UNIQUE KEY del código
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código del proyecto ya existe. Por favor asigne uno diferente.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar el proyecto: ' . $e->getMessage()];
        }
    }

    public function update(ProyectoTO $proyecto, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($proyecto->getClienteId() !== null) {
            $campos[] = 'lInCli_cont = :clienteId';
            $parametros[':clienteId'] = $proyecto->getClienteId();
        }
        if ($proyecto->getSedeId() !== null) {
            $campos[] = 'lInSed_cont = :sedeId';
            $parametros[':sedeId'] = $proyecto->getSedeId();
        }
        if ($proyecto->getVisitaId() !== null) {
            $campos[] = 'lInVis_cont = :visitaId';
            $parametros[':visitaId'] = $proyecto->getVisitaId();
        }
        if ($proyecto->getCodigo() !== null) {
            $campos[] = 'lStPro_codi = :codigo';
            $parametros[':codigo'] = $proyecto->getCodigo();
        }
        if ($proyecto->getNombre() !== null) {
            $campos[] = 'lStPro_nomb = :nombre';
            $parametros[':nombre'] = $proyecto->getNombre();
        }
        if ($proyecto->getEstado() !== null) {
            $campos[] = 'lStPro_esta = :estado';
            $parametros[':estado'] = $proyecto->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE proyectos SET " . implode(', ', $campos) . " WHERE lInPro_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Proyecto actualizado exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código de proyecto ingresado ya pertenece a otro registro.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar el proyecto.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado ENUM a 'RECHAZADO'
        $sql = "UPDATE proyectos SET lStPro_esta = 'RECHAZADO' WHERE lInPro_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Proyecto cancelado/rechazado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El proyecto no existe o ya estaba rechazado'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al cancelar el proyecto.'];
        }
    }
}