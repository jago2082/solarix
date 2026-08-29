<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\PlantillaSeccionTO;
use PDO;

class PlantillaSeccionDAO {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Traemos solo las secciones activas (A).
        $sql = "SELECT 
                    lInPse_cont AS id,
                    lInPtd_cont AS plantillaId,
                    lStPse_codi AS codigo,
                    lStPse_titu AS titulo,
                    lInPse_orde AS orden,
                    lStPse_cont AS contenido,
                    lStPse_acti AS estado
                FROM plantilla_secciones 
                WHERE lStPse_acti = 'A'";
                
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
                    lInPse_cont AS id,
                    lInPtd_cont AS plantillaId,
                    lStPse_codi AS codigo,
                    lStPse_titu AS titulo,
                    lInPse_orde AS orden,
                    lStPse_cont AS contenido,
                    lStPse_acti AS estado
                FROM plantilla_secciones 
                WHERE lInPse_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(PlantillaSeccionTO $seccion) {
        $sql = "INSERT INTO plantilla_secciones (
                    lInPtd_cont, lStPse_codi, lStPse_titu, 
                    lInPse_orde, lStPse_cont, lStPse_acti
                ) VALUES (
                    :plantillaId, :codigo, :titulo, 
                    :orden, :contenido, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':plantillaId', $seccion->getPlantillaId(), PDO::PARAM_INT);
            $stmt->bindValue(':codigo', $seccion->getCodigo());
            $stmt->bindValue(':titulo', $seccion->getTitulo());
            $stmt->bindValue(':orden', $seccion->getOrden(), PDO::PARAM_INT);
            $stmt->bindValue(':contenido', $seccion->getContenido());
            $stmt->bindValue(':estado', $seccion->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Sección de plantilla registrada exitosamente'];
        } catch (\PDOException $e) {
            // Manejamos la violación del UNIQUE KEY (uk_plantilla_secciones_codigo)
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código de la sección ya existe para esta plantilla. Por favor ingrese uno diferente.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar la sección de la plantilla.'];
        }
    }

    public function update(PlantillaSeccionTO $seccion, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($seccion->getPlantillaId() !== null) {
            $campos[] = 'lInPtd_cont = :plantillaId';
            $parametros[':plantillaId'] = $seccion->getPlantillaId();
        }
        if ($seccion->getCodigo() !== null) {
            $campos[] = 'lStPse_codi = :codigo';
            $parametros[':codigo'] = $seccion->getCodigo();
        }
        if ($seccion->getTitulo() !== null) {
            $campos[] = 'lStPse_titu = :titulo';
            $parametros[':titulo'] = $seccion->getTitulo();
        }
        if ($seccion->getOrden() !== null) {
            $campos[] = 'lInPse_orde = :orden';
            $parametros[':orden'] = $seccion->getOrden();
        }
        if ($seccion->getContenido() !== null) {
            $campos[] = 'lStPse_cont = :contenido';
            $parametros[':contenido'] = $seccion->getContenido();
        }
        if ($seccion->getEstado() !== null) {
            $campos[] = 'lStPse_acti = :estado';
            $parametros[':estado'] = $seccion->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE plantilla_secciones SET " . implode(', ', $campos) . " WHERE lInPse_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Sección de plantilla actualizada exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código ingresado ya pertenece a otra sección en esta misma plantilla.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar la sección de la plantilla.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado a 'I' (Inactiva)
        $sql = "UPDATE plantilla_secciones SET lStPse_acti = 'I' WHERE lInPse_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Sección de plantilla desactivada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La sección no existe o ya estaba inactiva'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar la sección de la plantilla.'];
        }
    }
}