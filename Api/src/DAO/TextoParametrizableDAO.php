<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\TextoParametrizableTO;
use PDO;

class TextoParametrizableDAO {
    private $conn; 

    public function __construct() {

        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Alias de columnas. Traemos solo los textos activos (estado = 1).
        $sql = "SELECT 
                    lInTxp_cont AS id,
                    lStTxp_codi AS codigo,
                    lStTxp_titu AS titulo,
                    lStTxp_cont AS contenido,
                    lStTxt_esta AS estado
                FROM textos_parametrizables 
                WHERE lStTxt_esta = 1";
                
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
                    lInTxp_cont AS id,
                    lStTxp_codi AS codigo,
                    lStTxp_titu AS titulo,
                    lStTxp_cont AS contenido,
                    lStTxt_esta AS estado
                FROM textos_parametrizables 
                WHERE lInTxp_cont = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(TextoParametrizableTO $texto) {
        $sql = "INSERT INTO textos_parametrizables (
                    lStTxp_codi, lStTxp_titu, lStTxp_cont, lStTxt_esta
                ) VALUES (
                    :codigo, :titulo, :contenido, :estado
                )";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':codigo', $texto->getCodigo());
            $stmt->bindValue(':titulo', $texto->getTitulo());
            $stmt->bindValue(':contenido', $texto->getContenido());
            $stmt->bindValue(':estado', $texto->getEstado(), PDO::PARAM_INT);
            $stmt->execute();
            
            return ['status' => 'success', 'message' => 'Texto parametrizable registrado exitosamente'];
        } catch (\PDOException $e) {
            // Manejamos la violación de código único
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código del texto ya existe. Por favor use uno diferente.'];
            }
            return ['status' => 'error', 'message' => 'Error al registrar el texto: ' . $e->getMessage()];
        }
    }

    public function update(TextoParametrizableTO $texto, $id) {
        $campos = [];
        $parametros = [':id' => $id];

        if ($texto->getCodigo() !== null) {
            $campos[] = 'lStTxp_codi = :codigo';
            $parametros[':codigo'] = $texto->getCodigo();
        }
        if ($texto->getTitulo() !== null) {
            $campos[] = 'lStTxp_titu = :titulo';
            $parametros[':titulo'] = $texto->getTitulo();
        }
        if ($texto->getContenido() !== null) {
            $campos[] = 'lStTxp_cont = :contenido';
            $parametros[':contenido'] = $texto->getContenido();
        }
        if ($texto->getEstado() !== null) {
            $campos[] = 'lStTxt_esta = :estado';
            $parametros[':estado'] = $texto->getEstado();
        }

        if (empty($campos)) {
            return ['status' => 'error', 'message' => 'No hay datos para actualizar'];
        }

        $sql = "UPDATE textos_parametrizables SET " . implode(', ', $campos) . " WHERE lInTxp_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return ['status' => 'success', 'message' => 'Texto parametrizable actualizado exitosamente'];
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'El código de texto ingresado ya pertenece a otro registro.'];
            }
            return ['status' => 'error', 'message' => 'Error al actualizar el texto.'];
        }
    }

    public function delete($id) {
        // Borrado Lógico cambiando el estado a 0 (Inactivo)
        $sql = "UPDATE textos_parametrizables SET lStTxt_esta = 0 WHERE lInTxp_cont = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Texto parametrizable desactivado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El texto no existe o ya estaba inactivo'];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => 'Error al desactivar el texto.'];
        }
    }
}