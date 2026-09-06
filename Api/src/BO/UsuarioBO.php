<?php
namespace App\BO;
use App\DAO\UsuarioDAO;
use App\TO\UsuarioTO;
use Firebase\JWT\JWT;

class UsuarioBO {
    private $dao;
    
    public function __construct() { 
        $this->dao = new UsuarioDAO(); 
    }
    
    public function getAll() { 
        return $this->dao->getAll(); 
    }
    
    public function getById($id) { 
        return $this->dao->getById($id); 
    }
    
    public function create(UsuarioTO $usuarioTO) 
    {
        return $this->dao->create($usuarioTO); 
    }
    
    public function update(UsuarioTO $usuarioTO, $id) {
        if (empty($id)) {
            return ['status' => 'error', 'message' => 'El ID del usuario es requerido'];
        }
        return $this->dao->update($usuarioTO, $id);
    }

    /**
     * @return array
     */
    public function delete($id) {
        return $this->dao->delete($id);
    }

    public function login(UsuarioTO $usuarioTO) {
        $credencial = $usuarioTO->getEmail();     
        $passwordPlana = $usuarioTO->getPassword();

        // 2. Buscar en la Base de Datos (a través del DAO)
        $usuarioBD = $this->dao->obtenerPorCredencial($credencial);
        
        // Si no existe o está inactivo
        if (!$usuarioBD) {
            return [
                'status' => 'error', 
                'message' => 'Usuario no encontrado o inactivo', 
                'code' => 404
            ];
        }

        // 3. Verificar contraseña contra el Hash de la BD
        if (password_verify($passwordPlana, $usuarioBD['password'])) {
            
            // 4. Crear el Token JWT localmente
            $key = 'TU_CLAVE_SECRETA_SUPER_SEGURA_123!'; // ¡Cambia esto en producción!
            
            $payload = [
                'iss' => 'tu_dominio_o_api.com', // Quién emite el token
                'iat' => time(), // Cuándo se emite
                'exp' => time() + (60 * 60 * 2), // Fecha de expiración (2 horas)
                'data' => [
                    'id' => $usuarioBD['id'],
                    'nombres' => $usuarioBD['nombres'],
                    'email' => $usuarioBD['email']
                ]
            ];

            // Generación matemática del string del Token
            $jwt = JWT::encode($payload, $key, 'HS256');

            return [
                'status' => 'success', 
                'message' => 'Sesión iniciada correctamente',
                'token' => $jwt, 
                'rol' => $usuarioBD['rol'],
                'code' => 200
            ];

        } else {
            // Contraseña incorrecta
            return [
                'status' => 'error', 
                'message' => 'Credenciales incorrectas', 
                'code' => 401 // Unauthorized
            ];
        }
    }
}