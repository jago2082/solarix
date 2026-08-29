<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\PlantillaVariableBO;
use App\TO\PlantillaVariableTO;

class PlantillaVariableController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new PlantillaVariableBO(); 
    }

    /**
     * @OA\Get(
     *     path="/plantilla-variables",
     *     summary="Obtener todas las variables de plantilla",
     *     tags={"PlantillaVariables"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de variables obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PlantillaVariableTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/plantilla-variables/{id}",
     *     summary="Obtener variable de plantilla por ID",
     *     tags={"PlantillaVariables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la variable de plantilla",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variable de plantilla encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaVariableTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Variable de plantilla no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Variable de plantilla no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/plantilla-variables",
     *     summary="Crear una nueva variable de plantilla",
     *     tags={"PlantillaVariables"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una variable de plantilla",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaVariableTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Variable de plantilla creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['seccionId']) || empty(trim($data['codigo'] ?? '')) || empty(trim($data['tipo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'La sección, el código y el tipo son obligatorios'], 400);
        }

        $variableTO = new PlantillaVariableTO();
        $variableTO->setSeccionId((int)$data['seccionId']);
        $variableTO->setCodigo(trim($data['codigo']));
        $variableTO->setNombre(isset($data['nombre']) ? trim($data['nombre']) : null);
        $variableTO->setTipo(trim($data['tipo']));
        $variableTO->setFormato(isset($data['formato']) ? trim($data['formato']) : null);

        $resultado = $this->bo->create($variableTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/plantilla-variables/{id}",
     *     summary="Actualizar una variable de plantilla existente",
     *     tags={"PlantillaVariables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la variable a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la variable de plantilla",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaVariableTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variable de plantilla actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la variable de plantilla"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $variableTO = new PlantillaVariableTO();

        // Mapeo dinámico
        if (isset($data['seccionId'])) $variableTO->setSeccionId((int)$data['seccionId']);
        if (isset($data['codigo'])) $variableTO->setCodigo(trim($data['codigo']));
        if (isset($data['nombre'])) $variableTO->setNombre(trim($data['nombre']));
        if (isset($data['tipo'])) $variableTO->setTipo(trim($data['tipo']));
        if (isset($data['formato'])) $variableTO->setFormato(trim($data['formato']));

        $resultado = $this->bo->update($variableTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/plantilla-variables/{id}",
     *     summary="Eliminar o desactivar variable de plantilla por ID",
     *     tags={"PlantillaVariables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la variable a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variable de plantilla eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo eliminar la variable de plantilla"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}