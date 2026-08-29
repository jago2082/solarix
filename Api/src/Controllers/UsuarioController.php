<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\UsuarioBO;
use App\TO\UsuarioTO;

class UsuarioController
{    
    private $bo;

    public function __construct()
    {
        $this->bo = new UsuarioBO();
    }

    /**
     * @OA\Get(
     *     path="/usuarios",
     *     summary="Obtener todos los usuarios activos",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UsuarioTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res)
    {
        $data = $this->bo->getAll();

        // Retornamos directamente el arreglo, Slim lo convertirá a un JSON limpio
        return $res->withJson($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/usuarios/{id}",
     *     summary="Obtener usuario por ID",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args)
    {
        $data = $this->bo->getById($args['id']);

        if ($data) {
            return $res->withJson($data, 200);
        } else {
            return $res->withJson([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/usuarios",
     *     summary="Crear un nuevo usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos requeridos para el registro de un nuevo usuario",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o formato inválido"
     *     )
     * )
     */
    public function create(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();

        // 1. Validaciones de campos NOT NULL en la base de datos
        if (empty($data['nombres']) || empty($data['codigo']) || empty($data['email']) || empty($data['password'])) {
            return $response->withJson(['error' => 'Los campos nombres, codigo, email y password son obligatorios'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $response->withJson(['error' => 'El formato del email no es válido'], 400);
        }

        // 2. Llenar el Objeto de Transferencia (TO)
        $usuarioTO = new UsuarioTO();
        $usuarioTO->setNombres(trim($data['nombres']));
        $usuarioTO->setApellidos(isset($data['apellidos']) ? trim($data['apellidos']) : null);

        // Autogenerar Nombre Completo (Nombres + Apellidos)
        $nombreCompleto = trim($data['nombres'] . ' ' . ($data['apellidos'] ?? ''));
        $usuarioTO->setNombreCompleto($nombreCompleto);

        $usuarioTO->setCodigo(trim($data['codigo']));
        $usuarioTO->setEmail(trim($data['email']));

        // Encriptar la contraseña
        $usuarioTO->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $usuarioTO->setTelefono(isset($data['telefono']) ? trim($data['telefono']) : null);

        // Estado por defecto Activo 'A'
        $usuarioTO->setEstado(isset($data['estado']) ? $data['estado'] : 'A');

        // 3. Enviar al BO
        $usuarioBO = new UsuarioBO();
        $resultado = $usuarioBO->create($usuarioTO);

        return $response->withJson($resultado, $resultado['status'] === 'success' ? 201 : 400);
    }

    /**
     * @OA\Put(
     *     path="/usuarios/{id}",
     *     summary="Actualizar un usuario existente",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del usuario",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar usuario"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args)
    {
        // Usamos getParsedBody() que es el estándar en Slim para capturar el JSON
        $data = $req->getParsedBody();
        $usuarioTO = new UsuarioTO();

        // Mapeo condicional: Solo seteamos lo que el frontend haya enviado
        if (isset($data['nombres']))
            $usuarioTO->setNombres(trim($data['nombres']));
        if (isset($data['apellidos']))
            $usuarioTO->setApellidos(trim($data['apellidos']));

        // Si enviaron nombres o apellidos, recalculamos el nombre completo
        if (isset($data['nombres']) || isset($data['apellidos'])) {
            $nombreCompleto = trim(($data['nombres'] ?? '') . ' ' . ($data['apellidos'] ?? ''));
            $usuarioTO->setNombreCompleto($nombreCompleto);
        }

        if (isset($data['codigo']))
            $usuarioTO->setCodigo(trim($data['codigo']));
        if (isset($data['email']))
            $usuarioTO->setEmail(trim($data['email']));
        if (isset($data['telefono']))
            $usuarioTO->setTelefono(trim($data['telefono']));
        if (isset($data['estado']))
            $usuarioTO->setEstado(trim($data['estado']));

        // Si deciden actualizar la contraseña, la hasheamos de una vez
        if (!empty($data['password'])) {
            $usuarioTO->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }

        $resultado = $this->bo->update($usuarioTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/usuarios/{id}",
     *     summary="Desactivar (borrado lógico) usuario por ID",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el usuario"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args)
    {
        // Llamamos al BO (que a su vez llama al DAO)
        $resultado = $this->bo->delete($args['id']);

        // Retornamos 200 OK si fue exitoso, o 400 Bad Request si hubo un error
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Post(
     *     path="/usuarios/login",
     *     summary="Autenticación de usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciales de acceso del usuario",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="juan.perez@empresa.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Secret123*")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa, retorna token o sesión"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros insuficientes o mal formateados"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas"
     *     )
     * )
     */
    public function login(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();

        // 1. Validar que vengan los datos desde Postman
        // Usaremos "usuario" genéricamente, el cual puede ser el email o el código
        if (empty($data['email']) || empty($data['password'])) {
            return $response->withJson(['error' => 'Usuario/Email y contraseña requeridos'], 400);
        }

        // 2. Usar el TO como mensajero
        $usuarioTO = new UsuarioTO();
        // Guardamos el dato ingresado en el campo 'Codigo' para transportarlo
        $usuarioTO->setEmail(trim($data['email']));
        $usuarioTO->setPassword($data['password']); // Contraseña plana, sin hashear aún

        // 3. Enviar a la capa de Negocio
        $usuarioBO = new UsuarioBO();
        $resultado = $usuarioBO->login($usuarioTO);

        // Extraemos el código de estado HTTP (200, 401, 404) que nos devuelve el BO
        $statusCode = $resultado['code'];
        unset($resultado['code']); // Lo quitamos del arreglo para no imprimirlo en el JSON de respuesta

        return $response->withJson($resultado, $statusCode);
    }
}