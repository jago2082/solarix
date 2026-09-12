<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\SesionTO;
use PDO;

class SesionDAO {
    private $conn;

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas y filtro por sesiones activas
        $sql = "SELECT 
                    lInSes_cont AS id,
                    lInUsu_cont AS usuarioId,
                    lStSes_tken AS token,
                    lDtSes_fini AS fechaInicio,
                    lDtSes_fexp AS fechaExpiracion,
                    lDtSes_fuin AS fechaUltimoUso,
                    lStSes_nuip AS ip,
                    lStSes_nodi AS dispositivo,
                    lStSes_esta AS estado
                FROM sesiones 
                WHERE lStSes_esta = 'A'";
                
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
                    lInSes_cont AS id,
                    lInUsu_cont AS usuarioId,
                    lStSes_tken AS token,
                    lDtSes_fini AS fechaInicio,
                    lDtSes_fexp AS fechaExpiracion,
                    lDtSes_fuin AS fechaUltimoUso,
                    lStSes_nuip AS ip,
                    lStSes_nodi AS dispositivo,
                    lStSes_esta AS estado
                FROM sesiones 
                WHERE lInSes_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(SesionTO $sesion) {
        $sql = "INSERT INTO sesiones (
                    lInUsu_cont, 
                    lStSes_tken, 
                    lDtSes_fexp, 
                    lStSes_nuip, 
                    lStSes_nodi, 
                    lStSes_esta
                ) VALUES (
                    :usuarioId, 
                    :token, 
                    :fechaExpiracion, 
                    :ip, 
                    :dispositivo, 
                    :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':usuarioId', $sesion->getUsuarioId());
            $stmt->bindValue(':token', $sesion->getToken());
            $stmt->bindValue(':fechaExpiracion', $sesion->getFechaExpiracion());
            $stmt->bindValue(':ip', $sesion->getIp());
            $stmt->bindValue(':dispositivo', $sesion->getDispositivo());
            $stmt->bindValue(':estado', $sesion->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Sesión registrada exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Este token ya existe en la base de datos.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar la sesión.'];
        }
    }

    public function update(SesionTO $sesion, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($sesion->getFechaExpiracion() !== null) {
            $campos[] = 'lDtSes_fexp = :fechaExpiracion';
            $parametros[':fechaExpiracion'] = $sesion->getFechaExpiracion();
        }
        if ($sesion->getFechaUltimoUso() !== null) {
            $campos[] = 'lDtSes_fuin = :fechaUltimoUso';
            $parametros[':fechaUltimoUso'] = $sesion->getFechaUltimoUso();
        }
        if ($sesion->getEstado() !== null) {
            $campos[] = 'lStSes_esta = :estado';
            $parametros[':estado'] = $sesion->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE sesiones SET " . implode(', ', $campos) . " WHERE lInSes_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Sesión actualizada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar la sesión.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico: Pasamos el estado de la sesión a Inactiva ('I')
        $sql = "UPDATE sesiones SET lStSes_esta = 'I' WHERE lInSes_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Sesión terminada (inactivada) exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La sesión no existe o ya estaba inactiva'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar la sesión.'];
        }
    }
}