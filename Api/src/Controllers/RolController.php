<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\RolBO;
use App\TO\RolTO;

class RolController {
    private $bo;

    public function __construct() {
        $this->bo = new RolBO();
    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     summary="Obtener todos los roles activos",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RolTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        $data = $this->bo->getAll();
        return $res->withJson($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/roles/{id}",
     *     summary="Obtener rol por ID",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/RolTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Rol no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     summary="Crear un nuevo rol",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un rol",
     *         @OA\JsonContent(ref="#/components/schemas/RolTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty(trim($data['nombre'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El nombre del rol es obligatorio'], 400);
        }

        $rolTO = new RolTO();
        $rolTO->setNombre(trim($data['nombre']));
        $rolTO->setDescripcion(isset($data['descripcion']) ? trim($data['descripcion']) : null);
        $rolTO->setEstado(isset($data['estado']) ? trim($data['estado']) : 'A');

        $resultado = $this->bo->create($rolTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     summary="Actualizar un rol existente",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del rol",
     *         @OA\JsonContent(ref="#/components/schemas/RolTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar el rol"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $rolTO = new RolTO();

        // Mapeo dinámico
        if (isset($data['nombre'])) $rolTO->setNombre(trim($data['nombre']));
        if (isset($data['descripcion'])) $rolTO->setDescripcion(trim($data['descripcion']));
        if (isset($data['estado'])) $rolTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($rolTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/roles/{id}",
     *     summary="Desactivar (borrado lógico) rol por ID",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el rol"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}