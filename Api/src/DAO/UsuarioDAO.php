<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\UsuarioTO;
use PDO;

class UsuarioDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT 
                    lInUsu_cont AS id,
                    lStUsu_nomb AS nombres,
                    lStUsu_apel AS apellidos,
                    lStUsu_noco AS nombreCompleto,
                    lStUsu_codi AS codigo,
                    lStUsu_emai AS email,
                    lStUsu_tele AS telefono,
                    lStUsu_esta AS estado,
                    lDtUsu_ulog AS ultimoLogin
                FROM usuarios 
                WHERE lStUsu_esta = 'A'";

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
                    lInUsu_cont AS id,
                    lStUsu_nomb AS nombres,
                    lStUsu_apel AS apellidos,
                    lStUsu_noco AS nombreCompleto,
                    lStUsu_codi AS codigo,
                    lStUsu_emai AS email,
                    lStUsu_tele AS telefono,
                    lStUsu_esta AS estado,
                    lDtUsu_ulog AS ultimoLogin
                FROM usuarios 
                WHERE lInUsu_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function create(UsuarioTO $usuario)
    {
        $sql = "INSERT INTO usuarios (
                    lStUsu_nomb, 
                    lStUsu_apel, 
                    lStUsu_noco, 
                    lStUsu_codi, 
                    lStUsu_emai, 
                    lStUsu_pass, 
                    lStUsu_tele, 
                    lStUsu_esta
                ) VALUES (
                    :nombres, 
                    :apellidos, 
                    :nombreCompleto, 
                    :codigo, 
                    :email, 
                    :password, 
                    :telefono, 
                    :estado
                )";

        try {
            $stmt = $this->conn->prepare($sql);

            // Mapeamos los datos del TO a la consulta SQL
            $stmt->bindValue(':nombres', $usuario->getNombres());
            $stmt->bindValue(':apellidos', $usuario->getApellidos());
            $stmt->bindValue(':nombreCompleto', $usuario->getNombreCompleto());
            $stmt->bindValue(':codigo', $usuario->getCodigo());
            $stmt->bindValue(':email', $usuario->getEmail());
            $stmt->bindValue(':password', $usuario->getPassword());
            $stmt->bindValue(':telefono', $usuario->getTelefono());
            $stmt->bindValue(':estado', $usuario->getEstado());

            $stmt->execute();

            return ['status' => 'success', 'message' => 'Usuario creado exitosamente'];

        } catch (\PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ];
        }
    }

public function update(UsuarioTO $usuario, $id)
    {
        // IMPORTANTE: Verifica que el nombre de tu llave primaria sea 'id'. 
        // Si en tu tabla se llama distinto (ej. 'lInUsu_id'), c¨¢mbialo en la l¨ªnea del WHERE.
        $sql = "UPDATE usuarios SET 
                    lStUsu_nomb = :nombres, 
                    lStUsu_apel = :apellidos, 
                    lStUsu_noco = :nombreCompleto, 
                    lStUsu_codi = :codigo, 
                    lStUsu_emai = :email, 
                    lStUsu_pass = :password, 
                    lStUsu_tele = :telefono, 
                    lStUsu_esta = :estado
                WHERE lInUsu_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);

            // Mapeamos los datos del TO a la consulta SQL
            $stmt->bindValue(':nombres', $usuario->getNombres());
            $stmt->bindValue(':apellidos', $usuario->getApellidos());
            $stmt->bindValue(':nombreCompleto', $usuario->getNombreCompleto());
            $stmt->bindValue(':codigo', $usuario->getCodigo());
            $stmt->bindValue(':email', $usuario->getEmail());
            $stmt->bindValue(':password', $usuario->getPassword());
            $stmt->bindValue(':telefono', $usuario->getTelefono());
            $stmt->bindValue(':estado', $usuario->getEstado());
            
            // Pasamos el ID que viene por la URL
            $stmt->bindValue(':id', $id);

            $stmt->execute();

            // rowCount() nos dice cu¨¢ntas filas fueron alteradas realmente
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Usuario actualizado exitosamente'];
            } else {
                return ['status' => 'warning', 'message' => 'No se encontraron cambios para hacer o el usuario no existe'];
            }

        } catch (\PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error al actualizar en la base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    public function delete($id): array
    {
        $sql = "UPDATE usuarios SET lStUsu_esta = 'I' WHERE lInUsu_cont = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute(); // Ejecutamos, pero NO le ponemos "return" a esta lÃ­nea

            // Verificamos si se actualizÃ³ el registro y retornamos EL ARREGLO
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Usuario desactivado exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'El usuario no existe o no se pudo modificar'];
            }

        } catch (\PDOException $e) {
            // Retornamos arreglo en caso de error
            return ['status' => 'error', 'message' => 'Error al desactivar: ' . $e->getMessage()];
        }
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT lInUsu_cont as id, lStUsu_nomb, lStUsu_emai, lStUsu_pass as password 
                FROM usuarios 
                WHERE lStUsu_emai = :email LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCredencial($usuarioOCorreo)
    {
        $sql = "SELECT usuarios.lInUsu_cont AS id, 
        usuarios.lStUsu_nomb AS nombres, 
        usuarios.lStUsu_emai AS email, 
        usuarios.lStUsu_pass AS password,
        roles.lStRol_nomb AS rol
        FROM usuarios 
        INNER JOIN usuario_roles
        ON usuario_roles.lInUsu_cont = usuarios.lInUsu_cont
        INNER JOIN roles
        ON roles.lInRol_cont = usuario_roles.lInRol_cont
        WHERE (usuarios.lStUsu_emai = :credencial OR usuarios.lStUsu_codi = :credencial) 
           AND usuarios.lStUsu_esta = 'A'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':credencial', $usuarioOCorreo);
        $stmt->execute();

        // Retorna el registro como arreglo asociativo (o false si no existe)
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}