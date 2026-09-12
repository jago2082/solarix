<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\VisitaTO;
use PDO;

class VisitaDAO
{
    private $conn;

    public function __construct()
    {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        // Traemos las visitas que NO estén canceladas (opcional, podrías traerlas todas si lo necesitas)
        $sql = "SELECT 
                        lInVis_cont AS id,
                        lInCli_cont AS clienteId,
                        lInSed_cont AS sedeId,
                        lInUsu_cont AS usuarioId,
                        lDtVis_fech AS fecha,
                        lStVis_obse AS observaciones,
                        lStVis_esta AS estado,
                        lDtVis_fecr AS fechaCreacion
                    FROM visitas 
                    WHERE lStVis_esta != 'CANCELADA'";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id)
    {
        $sql = "SELECT 
                        lInVis_cont AS id,
                        lInCli_cont AS clienteId,
                        lInSed_cont AS sedeId,
                        lInUsu_cont AS usuarioId,
                        lDtVis_fech AS fecha,
                        lStVis_obse AS observaciones,
                        lStVis_esta AS estado,
                        lDtVis_fecr AS fechaCreacion
                    FROM visitas 
                    WHERE lInVis_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(VisitaTO $visita)
    {
        $sql = "INSERT INTO visitas (
                        lInCli_cont, lInSed_cont, lInUsu_cont, 
                        lDtVis_fech, lStVis_obse, lStVis_esta
                    ) VALUES (
                        :clienteId, :sedeId, :usuarioId, 
                        :fecha, :observaciones, :estado
                    )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':clienteId', $visita->getClienteId());
            $stmt->bindValue(':sedeId', $visita->getSedeId());
            $stmt->bindValue(':usuarioId', $visita->getUsuarioId());
            $stmt->bindValue(':fecha', $visita->getFecha());
            $stmt->bindValue(':observaciones', $visita->getObservaciones());
            $stmt->bindValue(':estado', $visita->getEstado());
            $stmt->execute();

            return ['status' => 'success', 'message' => 'Visita programada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al programar la visita: ' . $e->getMessage()];
        }
    }

    public function update(VisitaTO $visita, $id)
    {
        $campos = [];
        $parametros = [':id' => $id];

        if ($visita->getClienteId() !== null) {
            $campos[] = 'lInCli_cont = :clienteId';
            $parametros[':clienteId'] = $visita->getClienteId();
        }
        if ($visita->getSedeId() !== null) {
            $campos[] = 'lInSed_cont = :sedeId';
            $parametros[':sedeId'] = $visita->getSedeId();
        }
        if ($visita->getUsuarioId() !== null) {
            $campos[] = 'lInUsu_cont = :usuarioId';
            $parametros[':usuarioId'] = $visita->getUsuarioId();
        }
        if ($visita->getFecha() !== null) {
            $campos[] = 'lDtVis_fech = :fecha';
            $parametros[':fecha'] = $visita->getFecha();
        }
        if ($visita->getObservaciones() !== null) {
            $campos[] = 'lStVis_obse = :observaciones';
            $parametros[':observaciones'] = $visita->getObservaciones();
        }
        if ($visita->getEstado() !== null) {
            $campos[] = 'lStVis_esta = :estado';
            $parametros[':estado'] = $visita->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE visitas SET " . implode(', ', $campos) . " WHERE lInVis_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Visita actualizada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar la visita.'];
        }
    }

    public function delete($id)
    {
        // Borrado Lógico cambiando el ENUM a 'CANCELADA'
        $sql = "UPDATE visitas SET lStVis_esta = 'CANCELADA' WHERE lInVis_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Visita cancelada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La visita no existe o ya estaba cancelada'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al cancelar la visita.'];
        }
    }
}