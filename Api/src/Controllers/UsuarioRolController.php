<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\UsuarioRolBO;
use App\TO\UsuarioRolTO;

class UsuarioRolController {
    private $bo;

    public function __construct() { 
        $this->bo = new UsuarioRolBO(); 
    }

    /**
     * @OA\Get(
     *     path="/usuario-roles",
     *     summary="Obtener todas las asignaciones de rol a usuario",
     *     tags={"UsuarioRoles"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UsuarioRolTO")
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
     *     path="/usuario-roles/{id}",
     *     summary="Obtener asignación usuario-rol por ID",
     *     tags={"UsuarioRoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la relación usuario-rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioRolTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Asignación no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/usuario-roles",
     *     summary="Asignar un rol a un usuario",
     *     tags={"UsuarioRoles"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="IDs del usuario y del rol a asociar",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioRolTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol asignado al usuario exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['usuarioId']) || empty($data['rolId'])) {
            return $res->withJson(['status' => 'error', 'message' => 'Los identificadores de usuario y rol son obligatorios'], 400);
        }

        $usuarioRolTO = new UsuarioRolTO();
        $usuarioRolTO->setUsuarioId($data['usuarioId']);
        $usuarioRolTO->setRolId($data['rolId']);

        $resultado = $this->bo->create($usuarioRolTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/usuario-roles/{id}",
     *     summary="Actualizar la asignación de rol de un usuario",
     *     tags={"UsuarioRoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Nuevos datos de la asignación usuario-rol",
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioRolTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la asignación"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $usuarioRolTO = new UsuarioRolTO();

        // Mapeo dinámico
        if (isset($data['usuarioId'])) $usuarioRolTO->setUsuarioId($data['usuarioId']);
        if (isset($data['rolId'])) $usuarioRolTO->setRolId($data['rolId']);

        $resultado = $this->bo->update($usuarioRolTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/usuario-roles/{id}",
     *     summary="Eliminar la asignación de un rol a un usuario",
     *     tags={"UsuarioRoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo eliminar la asignación"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}