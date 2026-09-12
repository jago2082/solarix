<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\ConfiguracionEmpresaTO;
use PDO;

class ConfiguracionEmpresaDAO {
    private $conn; 

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Traemos solo configuraciones activas.
        $sql = "SELECT 
                    lInCfe_cont AS id,
                    lStCfe_nomb AS nombre,
                    lStCfe_cnit AS nit,
                    lStCfe_dire AS direccion,
                    lStCfe_tele AS telefono,
                    lStCfe_emai AS email,
                    lStCfe_sweb AS sitioWeb,
                    lStCfe_logo AS logo,
                    lStCfe_desc AS descripcion,
                    lStCfe_esta AS estado
                FROM configuracion_empresa 
                WHERE lStCfe_esta = 'A'";
                
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
                    lInCfe_cont AS id,
                    lStCfe_nomb AS nombre,
                    lStCfe_cnit AS nit,
                    lStCfe_dire AS direccion,
                    lStCfe_tele AS telefono,
                    lStCfe_emai AS email,
                    lStCfe_sweb AS sitioWeb,
                    lStCfe_logo AS logo,
                    lStCfe_desc AS descripcion,
                    lStCfe_esta AS estado
                FROM configuracion_empresa 
                WHERE lInCfe_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(ConfiguracionEmpresaTO $config) {
        $sql = "INSERT INTO configuracion_empresa (
                    lStCfe_nomb, lStCfe_cnit, lStCfe_dire, 
                    lStCfe_tele, lStCfe_emai, lStCfe_sweb, 
                    lStCfe_logo, lStCfe_desc, lStCfe_esta
                ) VALUES (
                    :nombre, :nit, :direccion, 
                    :telefono, :email, :sitioWeb, 
                    :logo, :descripcion, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', $config->getNombre());
            $stmt->bindValue(':nit', $config->getNit());
            $stmt->bindValue(':direccion', $config->getDireccion());
            $stmt->bindValue(':telefono', $config->getTelefono());
            $stmt->bindValue(':email', $config->getEmail());
            $stmt->bindValue(':sitioWeb', $config->getSitioWeb());
            $stmt->bindValue(':logo', $config->getLogo());
            $stmt->bindValue(':descripcion', $config->getDescripcion());
            $stmt->bindValue(':estado', $config->getEstado());
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Configuración de empresa registrada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al registrar la configuración: ' . $e->getMessage()];
        }
    }

    public function update(ConfiguracionEmpresaTO $config, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($config->getNombre() !== null) {
            $campos[] = 'lStCfe_nomb = :nombre';
            $parametros[':nombre'] = $config->getNombre();
        }
        if ($config->getNit() !== null) {
            $campos[] = 'lStCfe_cnit = :nit';
            $parametros[':nit'] = $config->getNit();
        }
        if ($config->getDireccion() !== null) {
            $campos[] = 'lStCfe_dire = :direccion';
            $parametros[':direccion'] = $config->getDireccion();
        }
        if ($config->getTelefono() !== null) {
            $campos[] = 'lStCfe_tele = :telefono';
            $parametros[':telefono'] = $config->getTelefono();
        }
        if ($config->getEmail() !== null) {
            $campos[] = 'lStCfe_emai = :email';
            $parametros[':email'] = $config->getEmail();
        }
        if ($config->getSitioWeb() !== null) {
            $campos[] = 'lStCfe_sweb = :sitioWeb';
            $parametros[':sitioWeb'] = $config->getSitioWeb();
        }
        if ($config->getLogo() !== null) {
            $campos[] = 'lStCfe_logo = :logo';
            $parametros[':logo'] = $config->getLogo();
        }
        if ($config->getDescripcion() !== null) {
            $campos[] = 'lStCfe_desc = :descripcion';
            $parametros[':descripcion'] = $config->getDescripcion();
        }
        if ($config->getEstado() !== null) {
            $campos[] = 'lStCfe_esta = :estado';
            $parametros[':estado'] = $config->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE configuracion_empresa SET " . implode(', ', $campos) . " WHERE lInCfe_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Configuración actualizada exitosamente'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al actualizar la configuración.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado a 'I' (Inactivo)
        $sql = "UPDATE configuracion_empresa SET lStCfe_esta = 'I' WHERE lInCfe_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Configuración desactivada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'La configuración no existe o ya estaba inactiva'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar la configuración.'];
        }
    }
}