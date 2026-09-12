<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\ContactoTO;
use PDO;

class ContactoDAO {
    private $conn;

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias para nombres amigables. Solo contactos activos.
        $sql = "SELECT 
                    lInCon_cont AS id,
                    lInCli_cont AS clienteId,
                    lStCon_nomb AS nombre,
                    lStCon_carg AS cargo,
                    lStCon_tele AS telefono,
                    lStCon_emai AS email,
                    lStCon_prin AS principal,
                    lStCon_esta AS estado
                FROM contactos 
                WHERE lStCon_esta = 'A'";
                
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
                    lInCon_cont AS id,
                    lInCli_cont AS clienteId,
                    lStCon_nomb AS nombre,
                    lStCon_carg AS cargo,
                    lStCon_tele AS telefono,
                    lStCon_emai AS email,
                    lStCon_prin AS principal,
                    lStCon_esta AS estado
                FROM contactos 
                WHERE lInCon_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(ContactoTO $contacto) {
        $sql = "INSERT INTO contactos (
                    lInCli_cont, lStCon_nomb, lStCon_carg, 
                    lStCon_tele, lStCon_emai, lStCon_prin, lStCon_esta
                ) VALUES (
                    :clienteId, :nombre, :cargo, 
                    :telefono, :email, :principal, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':clienteId', $contacto->getClienteId());
            $stmt->bindValue(':nombre', $contacto->getNombre());
            $stmt->bindValue(':cargo', $contacto->getCargo());
            $stmt->bindValue(':telefono', $contacto->getTelefono());
            $stmt->bindValue(':email', $contacto->getEmail());
            $stmt->bindValue(':principal', $contacto->getPrincipal(), PDO::PARAM_INT);
            $stmt->bindValue(':estado', $contacto->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Contacto registrado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar el contacto: ' . $e->getMessage()];
        }
    }

    public function update(ContactoTO $contacto, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($contacto->getClienteId() !== null) {
            $campos[] = 'lInCli_cont = :clienteId';
            $parametros[':clienteId'] = $contacto->getClienteId();
        }
        if ($contacto->getNombre() !== null) {
            $campos[] = 'lStCon_nomb = :nombre';
            $parametros[':nombre'] = $contacto->getNombre();
        }
        if ($contacto->getCargo() !== null) {
            $campos[] = 'lStCon_carg = :cargo';
            $parametros[':cargo'] = $contacto->getCargo();
        }
        if ($contacto->getTelefono() !== null) {
            $campos[] = 'lStCon_tele = :telefono';
            $parametros[':telefono'] = $contacto->getTelefono();
        }
        if ($contacto->getEmail() !== null) {
            $campos[] = 'lStCon_emai = :email';
            $parametros[':email'] = $contacto->getEmail();
        }
        if ($contacto->getPrincipal() !== null) {
            $campos[] = 'lStCon_prin = :principal';
            $parametros[':principal'] = $contacto->getPrincipal();
        }
        if ($contacto->getEstado() !== null) {
            $campos[] = 'lStCon_esta = :estado';
            $parametros[':estado'] = $contacto->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE contactos SET " . implode(', ', $campos) . " WHERE lInCon_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Contacto actualizado exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar el contacto.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico
        $sql = "UPDATE contactos SET lStCon_esta = 'I' WHERE lInCon_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Contacto desactivado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El contacto no existe o ya está inactivo'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar el contacto.'];
        }
    }
}
