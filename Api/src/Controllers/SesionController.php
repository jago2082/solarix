<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\SesionBO;
use App\TO\SesionTO;

class SesionController {
    private $bo;

    public function __construct() { 
        $this->bo = new SesionBO(); 
    }

    /**
     * @OA\Get(
     *     path="/sesiones",
     *     summary="Obtener todas las sesiones activas",
     *     tags={"Sesiones"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sesiones obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SesionTO")
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
     *     path="/sesiones/{id}",
     *     summary="Obtener sesión por ID",
     *     tags={"Sesiones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sesión",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesión encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/SesionTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sesión no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Sesión no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/sesiones",
     *     summary="Crear o registrar una nueva sesión",
     *     tags={"Sesiones"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una sesión de usuario",
     *         @OA\JsonContent(ref="#/components/schemas/SesionTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sesión creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['usuarioId']) || empty($data['token']) || empty($data['fechaExpiracion'])) {
            return $res->withJson(['status' => 'error', 'message' => 'El usuario, token y fecha de expiración son obligatorios'], 400);
        }

        $sesionTO = new SesionTO();
        $sesionTO->setUsuarioId($data['usuarioId']);
        $sesionTO->setToken(trim($data['token']));
        $sesionTO->setFechaExpiracion(trim($data['fechaExpiracion']));
        $sesionTO->setIp($data['ip'] ?? null);
        $sesionTO->setDispositivo($data['dispositivo'] ?? null);
        $sesionTO->setEstado($data['estado'] ?? 'A');

        $resultado = $this->bo->create($sesionTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/sesiones/{id}",
     *     summary="Actualizar una sesión existente",
     *     tags={"Sesiones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sesión a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la sesión",
     *         @OA\JsonContent(ref="#/components/schemas/SesionTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesión actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la sesión"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $sesionTO = new SesionTO();

        // Mapeo dinámico
        if (isset($data['fechaExpiracion'])) $sesionTO->setFechaExpiracion(trim($data['fechaExpiracion']));
        if (isset($data['fechaUltimoUso'])) $sesionTO->setFechaUltimoUso(trim($data['fechaUltimoUso']));
        if (isset($data['estado'])) $sesionTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($sesionTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/sesiones/{id}",
     *     summary="Cerrar o desactivar sesión por ID",
     *     tags={"Sesiones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sesión a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesión desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la sesión"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}