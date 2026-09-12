<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\SedeTO;
use PDO;

class SedeDAO
{
    private $conn;

    public function __construct()
    {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        // Alias para nombres amigables. Solo sedes activas.
        $sql = "SELECT 
                        lInSed_cont AS id,
                        lInCli_cont AS clienteId,
                        lStSed_nomb AS nombre,
                        lStSed_dire AS direccion,
                        lStSed_ciud AS ciudad,
                        lStSed_depa AS departamento,
                        lDcSed_area AS area,
                        lStSed_esta AS estado
                    FROM sedes 
                    WHERE lStSed_esta = 'A'";

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
                        lInSed_cont AS id,
                        lInCli_cont AS clienteId,
                        lStSed_nomb AS nombre,
                        lStSed_dire AS direccion,
                        lStSed_ciud AS ciudad,
                        lStSed_depa AS departamento,
                        lDcSed_area AS area,
                        lStSed_esta AS estado
                    FROM sedes 
                    WHERE lInSed_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(SedeTO $sede)
    {
        $sql = "INSERT INTO sedes (
                        lInCli_cont, lStSed_nomb, lStSed_dire, 
                        lStSed_ciud, lStSed_depa, lDcSed_area, lStSed_esta
                    ) VALUES (
                        :clienteId, :nombre, :direccion, 
                        :ciudad, :departamento, :area, :estado
                    )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':clienteId', $sede->getClienteId());
            $stmt->bindValue(':nombre', $sede->getNombre());
            $stmt->bindValue(':direccion', $sede->getDireccion());
            $stmt->bindValue(':ciudad', $sede->getCiudad());
            $stmt->bindValue(':departamento', $sede->getDepartamento());
            $stmt->bindValue(':area', $sede->getArea());
            $stmt->bindValue(':estado', $sede->getEstado());
            $stmt->execute();

            return ['status' => 'success', 'message' => 'Sede registrada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar la sede: ' . $e->getMessage()];
        }
    }

    public function update(SedeTO $sede, $id)
    {
        $campos = [];
        $parametros = [':id' => $id];

        if ($sede->getClienteId() !== null) {
            $campos[] = 'lInCli_cont = :clienteId';
            $parametros[':clienteId'] = $sede->getClienteId();
        }
        if ($sede->getNombre() !== null) {
            $campos[] = 'lStSed_nomb = :nombre';
            $parametros[':nombre'] = $sede->getNombre();
        }
        if ($sede->getDireccion() !== null) {
            $campos[] = 'lStSed_dire = :direccion';
            $parametros[':direccion'] = $sede->getDireccion();
        }
        if ($sede->getCiudad() !== null) {
            $campos[] = 'lStSed_ciud = :ciudad';
            $parametros[':ciudad'] = $sede->getCiudad();
        }
        if ($sede->getDepartamento() !== null) {
            $campos[] = 'lStSed_depa = :departamento';
            $parametros[':departamento'] = $sede->getDepartamento();
        }
        if ($sede->getArea() !== null) {
            $campos[] = 'lDcSed_area = :area';
            $parametros[':area'] = $sede->getArea();
        }
        if ($sede->getEstado() !== null) {
            $campos[] = 'lStSed_esta = :estado';
            $parametros[':estado'] = $sede->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE sedes SET " . implode(', ', $campos) . " WHERE lInSed_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Sede actualizada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar la sede.'];
        }
    }

    public function delete($id)
    {
        // Borrado Lógico
        $sql = "UPDATE sedes SET lStSed_esta = 'I' WHERE lInSed_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Sede desactivada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La sede no existe o ya está inactiva'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar la sede.'];
        }
    }
}