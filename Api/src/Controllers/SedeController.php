<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\SedeBO;
use App\TO\SedeTO;

class SedeController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new SedeBO(); 
    }

    /**
     * @OA\Get(
     *     path="/sedes",
     *     summary="Obtener todas las sedes activas",
     *     tags={"Sedes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sedes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SedeTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/sedes/{id}",
     *     summary="Obtener sede por ID",
     *     tags={"Sedes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sede",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sede encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/SedeTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sede no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Sede no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/sedes",
     *     summary="Crear una nueva sede",
     *     tags={"Sedes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una sede",
     *         @OA\JsonContent(ref="#/components/schemas/SedeTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sede creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['clienteId']) || empty(trim($data['nombre'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El ID del cliente y el nombre de la sede son obligatorios'], 400);
        }

        $sedeTO = new SedeTO();
        $sedeTO->setClienteId($data['clienteId']);
        $sedeTO->setNombre(trim($data['nombre']));
        $sedeTO->setDireccion($data['direccion'] ?? null);
        $sedeTO->setCiudad($data['ciudad'] ?? null);
        $sedeTO->setDepartamento($data['departamento'] ?? null);
        $sedeTO->setArea(isset($data['area']) ? (float)$data['area'] : null);
        $sedeTO->setEstado($data['estado'] ?? 'A');

        $resultado = $this->bo->create($sedeTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/sedes/{id}",
     *     summary="Actualizar una sede existente",
     *     tags={"Sedes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sede a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la sede",
     *         @OA\JsonContent(ref="#/components/schemas/SedeTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sede actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la sede"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $sedeTO = new SedeTO();

        // Mapeo dinámico
        if (isset($data['clienteId'])) $sedeTO->setClienteId($data['clienteId']);
        if (isset($data['nombre'])) $sedeTO->setNombre(trim($data['nombre']));
        if (isset($data['direccion'])) $sedeTO->setDireccion(trim($data['direccion']));
        if (isset($data['ciudad'])) $sedeTO->setCiudad(trim($data['ciudad']));
        if (isset($data['departamento'])) $sedeTO->setDepartamento(trim($data['departamento']));
        if (isset($data['area'])) $sedeTO->setArea((float)$data['area']);
        if (isset($data['estado'])) $sedeTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($sedeTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/sedes/{id}",
     *     summary="Desactivar (borrado lógico) sede por ID",
     *     tags={"Sedes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sede a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sede desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la sede"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}