<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\PlanPpaTO;
use PDO;

class PlanPpaDAO {
    private $conn;  

    public function __construct() {
        $db = new Database();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Solo mostramos planes activos.
        $sql = "SELECT 
                    lInPpa_cont AS id,
                    lInPro_cont AS proyectoId,
                    lStPpa_nomb AS nombre,
                    lInPpa_duan AS duracionAnos,
                    lDcPpa_depo AS descuento,
                    lDcPpa_invi AS inversion,
                    lStPpa_inom AS incluyeOM,
                    lStPpa_incr AS incluyeRetie,
                    lStPpa_inle AS incluyeLegalizacion,
                    lStPpa_esta AS estado
                FROM planes_ppa 
                WHERE lStPpa_esta = 'A'";
                
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
                    lInPpa_cont AS id,
                    lInPro_cont AS proyectoId,
                    lStPpa_nomb AS nombre,
                    lInPpa_duan AS duracionAnos,
                    lDcPpa_depo AS descuento,
                    lDcPpa_invi AS inversion,
                    lStPpa_inom AS incluyeOM,
                    lStPpa_incr AS incluyeRetie,
                    lStPpa_inle AS incluyeLegalizacion,
                    lStPpa_esta AS estado
                FROM planes_ppa 
                WHERE lInPpa_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(PlanPpaTO $plan) {
        $sql = "INSERT INTO planes_ppa (
                    lInPro_cont, lStPpa_nomb, lInPpa_duan, 
                    lDcPpa_depo, lDcPpa_invi, lStPpa_inom, 
                    lStPpa_incr, lStPpa_inle, lStPpa_esta
                ) VALUES (
                    :proyectoId, :nombre, :duracionAnos, 
                    :descuento, :inversion, :incluyeOM, 
                    :incluyeRetie, :incluyeLegalizacion, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':proyectoId', $plan->getProyectoId());
            $stmt->bindValue(':nombre', $plan->getNombre());
            $stmt->bindValue(':duracionAnos', $plan->getDuracionAnos(), PDO::PARAM_INT);
            $stmt->bindValue(':descuento', $plan->getDescuento());
            $stmt->bindValue(':inversion', $plan->getInversion());
            $stmt->bindValue(':incluyeOM', $plan->getIncluyeOM());
            $stmt->bindValue(':incluyeRetie', $plan->getIncluyeRetie());
            $stmt->bindValue(':incluyeLegalizacion', $plan->getIncluyeLegalizacion());
            $stmt->bindValue(':estado', $plan->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Plan PPA registrado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar el Plan PPA: ' . $e->getMessage()];
        }
    }

    public function update(PlanPpaTO $plan, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($plan->getProyectoId() !== null) {
            $campos[] = 'lInPro_cont = :proyectoId';
            $parametros[':proyectoId'] = $plan->getProyectoId();
        }
        if ($plan->getNombre() !== null) {
            $campos[] = 'lStPpa_nomb = :nombre';
            $parametros[':nombre'] = $plan->getNombre();
        }
        if ($plan->getDuracionAnos() !== null) {
            $campos[] = 'lInPpa_duan = :duracionAnos';
            $parametros[':duracionAnos'] = $plan->getDuracionAnos();
        }
        if ($plan->getDescuento() !== null) {
            $campos[] = 'lDcPpa_depo = :descuento';
            $parametros[':descuento'] = $plan->getDescuento();
        }
        if ($plan->getInversion() !== null) {
            $campos[] = 'lDcPpa_invi = :inversion';
            $parametros[':inversion'] = $plan->getInversion();
        }
        if ($plan->getIncluyeOM() !== null) {
            $campos[] = 'lStPpa_inom = :incluyeOM';
            $parametros[':incluyeOM'] = $plan->getIncluyeOM();
        }
        if ($plan->getIncluyeRetie() !== null) {
            $campos[] = 'lStPpa_incr = :incluyeRetie';
            $parametros[':incluyeRetie'] = $plan->getIncluyeRetie();
        }
        if ($plan->getIncluyeLegalizacion() !== null) {
            $campos[] = 'lStPpa_inle = :incluyeLegalizacion';
            $parametros[':incluyeLegalizacion'] = $plan->getIncluyeLegalizacion();
        }
        if ($plan->getEstado() !== null) {
            $campos[] = 'lStPpa_esta = :estado';
            $parametros[':estado'] = $plan->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE planes_ppa SET " . implode(', ', $campos) . " WHERE lInPpa_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Plan PPA actualizado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar el plan PPA.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado a 'I'
        $sql = "UPDATE planes_ppa SET lStPpa_esta = 'I' WHERE lInPpa_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Plan PPA desactivado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El plan PPA no existe o ya estaba inactivo'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar el plan PPA.'];
        }
    }
}