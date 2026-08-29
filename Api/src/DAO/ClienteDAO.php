<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\ClienteTO;
use PDO;

    class ClienteDAO {
        private $conn;
    
        public function __construct() {
            $db = new Database();
            $this->conn = Database::getInstance()->getConnection();
        }
    
        public function getAll() {
            // Alias para ocultar los campos reales. Filtramos por estado 'A'.
            $sql = "SELECT 
                        lInCli_cont AS id,
                        lStCli_docu AS documento,
                        lStCli_noco AS nombreCompleto,
                        lStCli_tipo AS tipo,
                        lStCli_dire AS direccion,
                        lStCli_ciud AS ciudad,
                        lStCli_depa AS departamento,
                        lStCli_tele AS telefono,
                        lStCli_emai AS email,
                        lStCli_esta AS estado
                    FROM clientes 
                    WHERE lStCli_esta = 'A'";
                    
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
                        lInCli_cont AS id,
                        lStCli_docu AS documento,
                        lStCli_noco AS nombreCompleto,
                        lStCli_tipo AS tipo,
                        lStCli_dire AS direccion,
                        lStCli_ciud AS ciudad,
                        lStCli_depa AS departamento,
                        lStCli_tele AS telefono,
                        lStCli_emai AS email,
                        lStCli_esta AS estado
                    FROM clientes 
                    WHERE lInCli_cont = :id";
                    
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                return false;
            }
        }
    
        public function create(ClienteTO $cliente) {
            $sql = "INSERT INTO clientes (
                        lStCli_docu, lStCli_noco, lStCli_tipo, lStCli_dire, 
                        lStCli_ciud, lStCli_depa, lStCli_tele, lStCli_emai, lStCli_esta
                    ) VALUES (
                        :documento, :nombreCompleto, :tipo, :direccion, 
                        :ciudad, :departamento, :telefono, :email, :estado
                    )";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':documento', $cliente->getDocumento());
                $stmt->bindValue(':nombreCompleto', $cliente->getNombreCompleto());
                $stmt->bindValue(':tipo', $cliente->getTipo());
                $stmt->bindValue(':direccion', $cliente->getDireccion());
                $stmt->bindValue(':ciudad', $cliente->getCiudad());
                $stmt->bindValue(':departamento', $cliente->getDepartamento());
                $stmt->bindValue(':telefono', $cliente->getTelefono());
                $stmt->bindValue(':email', $cliente->getEmail());
                $stmt->bindValue(':estado', $cliente->getEstado());
                $stmt->execute();
                
                return ['status' => 'success', 'message' => 'Cliente registrado exitosamente'];
            } catch (\PDOException $e) {
                return ['status' => 'error', 'message' => 'Error al registrar el cliente.'];
            }
        }
    
        public function update(ClienteTO $cliente, $id) {
            $campos = [];
            $parametros = [':id' => $id];
    
            if ($cliente->getDocumento() !== null) {
                $campos[] = 'lStCli_docu = :documento';
                $parametros[':documento'] = $cliente->getDocumento();
            }
            if ($cliente->getNombreCompleto() !== null) {
                $campos[] = 'lStCli_noco = :nombreCompleto';
                $parametros[':nombreCompleto'] = $cliente->getNombreCompleto();
            }
            if ($cliente->getTipo() !== null) {
                $campos[] = 'lStCli_tipo = :tipo';
                $parametros[':tipo'] = $cliente->getTipo();
            }
            if ($cliente->getDireccion() !== null) {
                $campos[] = 'lStCli_dire = :direccion';
                $parametros[':direccion'] = $cliente->getDireccion();
            }
            if ($cliente->getCiudad() !== null) {
                $campos[] = 'lStCli_ciud = :ciudad';
                $parametros[':ciudad'] = $cliente->getCiudad();
            }
            if ($cliente->getDepartamento() !== null) {
                $campos[] = 'lStCli_depa = :departamento';
                $parametros[':departamento'] = $cliente->getDepartamento();
            }
            if ($cliente->getTelefono() !== null) {
                $campos[] = 'lStCli_tele = :telefono';
                $parametros[':telefono'] = $cliente->getTelefono();
            }
            if ($cliente->getEmail() !== null) {
                $campos[] = 'lStCli_emai = :email';
                $parametros[':email'] = $cliente->getEmail();
            }
            if ($cliente->getEstado() !== null) {
                $campos[] = 'lStCli_esta = :estado';
                $parametros[':estado'] = $cliente->getEstado();
            }
    
            if (empty($campos)) {
                return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
            }
    
            $sql = "UPDATE clientes SET " . implode(', ', $campos) . " WHERE lInCli_cont = :id";
    
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($parametros);
                return ['status' => 'success', 'message' => 'Cliente actualizado exitosamente'];
            } catch (\PDOException $e) {
                return ['status' => 'error', 'message' => 'Error al actualizar el cliente.'];
            }
        }
    
        public function delete($id) {
            // Borrado Lógico
            $sql = "UPDATE clientes SET lStCli_esta = 'I' WHERE lInCli_cont = :id";
            
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    return ['status' => 'success', 'message' => 'Cliente desactivado exitosamente'];
                } else {
                    return ['status' => 'error', 'message' => 'El cliente no existe o ya está inactivo'];
                }
            } catch (\PDOException $e) {
                return ['status' => 'error', 'message' => 'Error al desactivar el cliente.'];
            }
        }
    }