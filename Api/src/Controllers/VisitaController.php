<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\VisitaBO;
use App\TO\VisitaTO;

class VisitaController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new VisitaBO(); 
    }

    /**
     * @OA\Get(
     *     path="/visitas",
     *     summary="Obtener todas las visitas técnicas",
     *     tags={"Visitas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de visitas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VisitaTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/visitas/{id}",
     *     summary="Obtener visita técnica por ID",
     *     tags={"Visitas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la visita",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/VisitaTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Visita no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/visitas",
     *     summary="Crear o programar una nueva visita técnica",
     *     tags={"Visitas"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una visita",
     *         @OA\JsonContent(ref="#/components/schemas/VisitaTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Visita creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['clienteId']) || empty(trim($data['fecha'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El ID del cliente y la fecha de la visita son obligatorios'], 400);
        }

        $visitaTO = new VisitaTO();
        $visitaTO->setClienteId($data['clienteId']);
        $visitaTO->setSedeId($data['sedeId'] ?? null);
        $visitaTO->setUsuarioId($data['usuarioId'] ?? null);
        $visitaTO->setFecha(trim($data['fecha']));
        $visitaTO->setObservaciones($data['observaciones'] ?? null);
        $visitaTO->setEstado($data['estado'] ?? 'PROGRAMADA');

        $resultado = $this->bo->create($visitaTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/visitas/{id}",
     *     summary="Actualizar una visita técnica existente",
     *     tags={"Visitas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la visita a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la visita técnica",
     *         @OA\JsonContent(ref="#/components/schemas/VisitaTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la visita"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $visitaTO = new VisitaTO();

        // Mapeo dinámico
        if (isset($data['clienteId'])) $visitaTO->setClienteId($data['clienteId']);
        if (isset($data['sedeId'])) $visitaTO->setSedeId($data['sedeId']);
        if (isset($data['usuarioId'])) $visitaTO->setUsuarioId($data['usuarioId']);
        if (isset($data['fecha'])) $visitaTO->setFecha(trim($data['fecha']));
        if (isset($data['observaciones'])) $visitaTO->setObservaciones(trim($data['observaciones']));
        if (isset($data['estado'])) $visitaTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($visitaTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/visitas/{id}",
     *     summary="Desactivar o cancelar visita técnica por ID",
     *     tags={"Visitas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la visita a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la visita"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}