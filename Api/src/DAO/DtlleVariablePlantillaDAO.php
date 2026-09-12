<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\DtlleVariablePlantillaTO;
use PDO;

class DtlleVariablePlantillaDAO {
    private $conn;

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    private function selectColumns() {
        return "SELECT
                    lInDpl_cont AS id,
                    lInVpl_cont AS vplCont,
                    lInVpl_anop AS anoProyeccion,
                    lDcVpl_tcon AS tarifaConvencional,
                    lDcVpl_tppa AS tarifaPpa,
                    lDcVpl_coen AS consumoEnergia,
                    lDcVpl_geen AS generacionEnergia,
                    lDcVpl_cocu AS costoConsumoSinSsfv,
                    lDcVpl_crer AS costoRedRemanente,
                    lDcVpl_cssf AS costoSsfvPpa,
                    lDcVpl_cscr AS costoSsfvCostoRed,
                    lDcVpl_amil AS ahorroMillones,
                    lDtVpl_fecr AS fechaCreacion
                FROM dtlle_Variables_plantilla";
    }

    public function getAll() {
        $sql = $this->selectColumns();
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        $sql = $this->selectColumns() . " WHERE lInDpl_cont = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getByVplCont($vplCont) {
        $sql = $this->selectColumns() . " WHERE lInVpl_cont = :vplCont ORDER BY lInVpl_anop ASC";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':vplCont', $vplCont);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function create(DtlleVariablePlantillaTO $detalle) {
        $sql = "INSERT INTO dtlle_Variables_plantilla (
                    lInVpl_cont, lInVpl_anop, lDcVpl_tcon, lDcVpl_tppa,
                    lDcVpl_coen, lDcVpl_geen, lDcVpl_cocu, lDcVpl_crer,
                    lDcVpl_cssf, lDcVpl_cscr, lDcVpl_amil
                ) VALUES (
                    :vplCont, :anoProyeccion, :tarifaConvencional, :tarifaPpa,
                    :consumoEnergia, :generacionEnergia, :costoConsumoSinSsfv, :costoRedRemanente,
                    :costoSsfvPpa, :costoSsfvCostoRed, :ahorroMillones
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':vplCont', $detalle->getVplCont());
            $stmt->bindValue(':anoProyeccion', $detalle->getAnoProyeccion(), PDO::PARAM_INT);
            $stmt->bindValue(':tarifaConvencional', $detalle->getTarifaConvencional());
            $stmt->bindValue(':tarifaPpa', $detalle->getTarifaPpa());
            $stmt->bindValue(':consumoEnergia', $detalle->getConsumoEnergia());
            $stmt->bindValue(':generacionEnergia', $detalle->getGeneracionEnergia());
            $stmt->bindValue(':costoConsumoSinSsfv', $detalle->getCostoConsumoSinSsfv());
            $stmt->bindValue(':costoRedRemanente', $detalle->getCostoRedRemanente());
            $stmt->bindValue(':costoSsfvPpa', $detalle->getCostoSsfvPpa());
            $stmt->bindValue(':costoSsfvCostoRed', $detalle->getCostoSsfvCostoRed());
            $stmt->bindValue(':ahorroMillones', $detalle->getAhorroMillones());
            $stmt->execute();
            return ['status' => 'success', 'message' => 'Detalle registrado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar el detalle: ' . $e->getMessage()];
        }
    }

    public function update(DtlleVariablePlantillaTO $detalle, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($detalle->getVplCont() !== null) {
            $campos[] = 'lInVpl_cont = :vplCont';
            $parametros[':vplCont'] = $detalle->getVplCont();
        }
        if ($detalle->getAnoProyeccion() !== null) {
            $campos[] = 'lInVpl_anop = :anoProyeccion';
            $parametros[':anoProyeccion'] = $detalle->getAnoProyeccion();
        }
        if ($detalle->getTarifaConvencional() !== null) {
            $campos[] = 'lDcVpl_tcon = :tarifaConvencional';
            $parametros[':tarifaConvencional'] = $detalle->getTarifaConvencional();
        }
        if ($detalle->getTarifaPpa() !== null) {
            $campos[] = 'lDcVpl_tppa = :tarifaPpa';
            $parametros[':tarifaPpa'] = $detalle->getTarifaPpa();
        }
        if ($detalle->getConsumoEnergia() !== null) {
            $campos[] = 'lDcVpl_coen = :consumoEnergia';
            $parametros[':consumoEnergia'] = $detalle->getConsumoEnergia();
        }
        if ($detalle->getGeneracionEnergia() !== null) {
            $campos[] = 'lDcVpl_geen = :generacionEnergia';
            $parametros[':generacionEnergia'] = $detalle->getGeneracionEnergia();
        }
        if ($detalle->getCostoConsumoSinSsfv() !== null) {
            $campos[] = 'lDcVpl_cocu = :costoConsumoSinSsfv';
            $parametros[':costoConsumoSinSsfv'] = $detalle->getCostoConsumoSinSsfv();
        }
        if ($detalle->getCostoRedRemanente() !== null) {
            $campos[] = 'lDcVpl_crer = :costoRedRemanente';
            $parametros[':costoRedRemanente'] = $detalle->getCostoRedRemanente();
        }
        if ($detalle->getCostoSsfvPpa() !== null) {
            $campos[] = 'lDcVpl_cssf = :costoSsfvPpa';
            $parametros[':costoSsfvPpa'] = $detalle->getCostoSsfvPpa();
        }
        if ($detalle->getCostoSsfvCostoRed() !== null) {
            $campos[] = 'lDcVpl_cscr = :costoSsfvCostoRed';
            $parametros[':costoSsfvCostoRed'] = $detalle->getCostoSsfvCostoRed();
        }
        if ($detalle->getAhorroMillones() !== null) {
            $campos[] = 'lDcVpl_amil = :ahorroMillones';
            $parametros[':ahorroMillones'] = $detalle->getAhorroMillones();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE dtlle_Variables_plantilla SET " . implode(', ', $campos) . " WHERE lInDpl_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Detalle actualizado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar el detalle.'];
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM dtlle_Variables_plantilla WHERE lInDpl_cont = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Detalle eliminado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El detalle no existe'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar el detalle.'];
        }
    }

    public function deleteByVplCont($vplCont) {
        $sql = "DELETE FROM dtlle_Variables_plantilla WHERE lInVpl_cont = :vplCont";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':vplCont', $vplCont);
            $stmt->execute();
            return ['status' => 'success', 'message' => 'Detalles eliminados exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar los detalles.'];
        }
    }
}
