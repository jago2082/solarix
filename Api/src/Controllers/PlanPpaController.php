<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\PlanPpaBO;
use App\TO\PlanPpaTO;

class PlanPpaController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new PlanPpaBO(); 
    }

    /**
     * @OA\Get(
     *     path="/planes-ppa",
     *     summary="Obtener todos los planes PPA activos",
     *     tags={"PlanesPPA"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de planes PPA obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PlanPpaTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/planes-ppa/{id}",
     *     summary="Obtener plan PPA por ID",
     *     tags={"PlanesPPA"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del plan PPA",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan PPA encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/PlanPpaTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plan PPA no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Plan PPA no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/planes-ppa",
     *     summary="Crear un nuevo plan PPA",
     *     tags={"PlanesPPA"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un plan PPA",
     *         @OA\JsonContent(ref="#/components/schemas/PlanPpaTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan PPA creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['proyectoId']) || empty(trim($data['nombre'] ?? '')) || empty($data['duracionAnos'])) {
            return $res->withJson(['status' => 'error', 'message' => 'El proyecto, nombre y duración en años son obligatorios'], 400);
        }

        $planTO = new PlanPpaTO();
        $planTO->setProyectoId($data['proyectoId']);
        $planTO->setNombre(trim($data['nombre']));
        $planTO->setDuracionAnos((int)$data['duracionAnos']);
        $planTO->setDescuento(isset($data['descuento']) ? (float)$data['descuento'] : null);
        $planTO->setInversion(isset($data['inversion']) ? (float)$data['inversion'] : 0);
        $planTO->setIncluyeOM($data['incluyeOM'] ?? 'S');
        $planTO->setIncluyeRetie($data['incluyeRetie'] ?? 'S');
        $planTO->setIncluyeLegalizacion($data['incluyeLegalizacion'] ?? 'S');
        $planTO->setEstado($data['estado'] ?? 'A');

        $resultado = $this->bo->create($planTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/planes-ppa/{id}",
     *     summary="Actualizar un plan PPA existente",
     *     tags={"PlanesPPA"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del plan PPA a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del plan PPA",
     *         @OA\JsonContent(ref="#/components/schemas/PlanPpaTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan PPA actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar el plan PPA"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $planTO = new PlanPpaTO();

        // Mapeo dinámico
        if (isset($data['proyectoId'])) $planTO->setProyectoId($data['proyectoId']);
        if (isset($data['nombre'])) $planTO->setNombre(trim($data['nombre']));
        if (isset($data['duracionAnos'])) $planTO->setDuracionAnos((int)$data['duracionAnos']);
        if (isset($data['descuento'])) $planTO->setDescuento((float)$data['descuento']);
        if (isset($data['inversion'])) $planTO->setInversion((float)$data['inversion']);
        if (isset($data['incluyeOM'])) $planTO->setIncluyeOM(trim($data['incluyeOM']));
        if (isset($data['incluyeRetie'])) $planTO->setIncluyeRetie(trim($data['incluyeRetie']));
        if (isset($data['incluyeLegalizacion'])) $planTO->setIncluyeLegalizacion(trim($data['incluyeLegalizacion']));
        if (isset($data['estado'])) $planTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($planTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/planes-ppa/{id}",
     *     summary="Desactivar (borrado lógico) plan PPA por ID",
     *     tags={"PlanesPPA"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del plan PPA a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan PPA desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el plan PPA"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}