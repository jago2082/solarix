<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\VariablePlantillaTO;
use PDO;

class VariablePlantillaDAO {
    private $conn; 

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Mapeo seguro a la estructura del TO
        $sql = "SELECT 
                    lInVpl_cont AS id,
                    lInPpa_cont AS planPpaId,
                    lInVpl_anop AS anoProyeccion,
                    lDcVpl_tcon AS tarifaConvencional,
                    lDcVpl_coen AS consumoEnergia,
                    lDcVpl_geen AS generacionEnergia,
                    lDcVpl_crer AS costoRedRemanente,
                    lDtVpl_fecr AS fechaCreacion
                FROM variables_plantilla";
                
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
                    lInVpl_cont AS id,
                    lInPpa_cont AS planPpaId,
                    lInVpl_anop AS anoProyeccion,
                    lDcVpl_tcon AS tarifaConvencional,
                    lDcVpl_coen AS consumoEnergia,
                    lDcVpl_geen AS generacionEnergia,
                    lDcVpl_crer AS costoRedRemanente,
                    lDtVpl_fecr AS fechaCreacion
                FROM variables_plantilla 
                WHERE lInVpl_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(VariablePlantillaTO $variable) {
        $sql = "INSERT INTO variables_plantilla (
                    lInPpa_cont, lInVpl_anop, lDcVpl_tcon,
                    lDcVpl_coen, lDcVpl_geen, lDcVpl_crer
                ) VALUES (
                    :planPpaId, :anoProyeccion, :tarifaConvencional,
                    :consumoEnergia, :generacionEnergia, :costoRedRemanente
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':planPpaId', $variable->getPlanPpaId());
            $stmt->bindValue(':anoProyeccion', $variable->getAnoProyeccion(), PDO::PARAM_INT);
            $stmt->bindValue(':tarifaConvencional', $variable->getTarifaConvencional());
            $stmt->bindValue(':consumoEnergia', $variable->getConsumoEnergia());
            $stmt->bindValue(':generacionEnergia', $variable->getGeneracionEnergia());
            $stmt->bindValue(':costoRedRemanente', $variable->getCostoRedRemanente());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Variable de plantilla registrada exitosamente', 'id' => $this->conn->lastInsertId()];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar la variable: ' . $e->getMessage()];
        }
    }

    public function update(VariablePlantillaTO $variable, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($variable->getPlanPpaId() !== null) {
            $campos[] = 'lInPpa_cont = :planPpaId';
            $parametros[':planPpaId'] = $variable->getPlanPpaId();
        }
        if ($variable->getAnoProyeccion() !== null) {
            $campos[] = 'lInVpl_anop = :anoProyeccion';
            $parametros[':anoProyeccion'] = $variable->getAnoProyeccion();
        }
        if ($variable->getTarifaConvencional() !== null) {
            $campos[] = 'lDcVpl_tcon = :tarifaConvencional';
            $parametros[':tarifaConvencional'] = $variable->getTarifaConvencional();
        }
        if ($variable->getConsumoEnergia() !== null) {
            $campos[] = 'lDcVpl_coen = :consumoEnergia';
            $parametros[':consumoEnergia'] = $variable->getConsumoEnergia();
        }
        if ($variable->getGeneracionEnergia() !== null) {
            $campos[] = 'lDcVpl_geen = :generacionEnergia';
            $parametros[':generacionEnergia'] = $variable->getGeneracionEnergia();
        }
        if ($variable->getCostoRedRemanente() !== null) {
            $campos[] = 'lDcVpl_crer = :costoRedRemanente';
            $parametros[':costoRedRemanente'] = $variable->getCostoRedRemanente();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE variables_plantilla SET " . implode(', ', $campos) . " WHERE lInVpl_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Variable de plantilla actualizada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar la variable.'];
        }
    }

    public function delete($id) {
        // Borrado Físico (ya que no existe un campo de estado en esta tabla)
        $sql = "DELETE FROM variables_plantilla WHERE lInVpl_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Variable de plantilla eliminada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La variable no existe'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar la variable.'];
        }
    }
}
