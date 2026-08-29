<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\PlantillaDocumentoBO;
use App\TO\PlantillaDocumentoTO;

class PlantillaDocumentoController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new PlantillaDocumentoBO(); 
    }

    /**
     * @OA\Get(
     *     path="/plantillas-documento",
     *     summary="Obtener todas las plantillas de documento activas",
     *     tags={"PlantillasDocumento"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de plantillas de documento obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PlantillaDocumentoTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/plantillas-documento/{id}",
     *     summary="Obtener plantilla de documento por ID",
     *     tags={"PlantillasDocumento"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la plantilla de documento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plantilla de documento encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaDocumentoTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plantilla de documento no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Plantilla de documento no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/plantillas-documento",
     *     summary="Crear una nueva plantilla de documento",
     *     tags={"PlantillasDocumento"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una plantilla de documento",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaDocumentoTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plantilla de documento creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty(trim($data['nombre'] ?? '')) || empty(trim($data['codigo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El nombre y el código de la plantilla son obligatorios'], 400);
        }

        $plantillaTO = new PlantillaDocumentoTO();
        $plantillaTO->setNombre(trim($data['nombre']));
        $plantillaTO->setCodigo(trim($data['codigo']));
        $plantillaTO->setVersion(isset($data['version']) ? trim($data['version']) : null);
        $plantillaTO->setEstado(isset($data['estado']) ? trim($data['estado']) : 'A');

        $resultado = $this->bo->create($plantillaTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/plantillas-documento/{id}",
     *     summary="Actualizar una plantilla de documento existente",
     *     tags={"PlantillasDocumento"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la plantilla de documento a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la plantilla de documento",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaDocumentoTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plantilla de documento actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la plantilla de documento"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $plantillaTO = new PlantillaDocumentoTO();

        // Mapeo dinámico
        if (isset($data['nombre'])) $plantillaTO->setNombre(trim($data['nombre']));
        if (isset($data['codigo'])) $plantillaTO->setCodigo(trim($data['codigo']));
        if (isset($data['version'])) $plantillaTO->setVersion(trim($data['version']));
        if (isset($data['estado'])) $plantillaTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($plantillaTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/plantillas-documento/{id}",
     *     summary="Desactivar (borrado lógico) plantilla de documento por ID",
     *     tags={"PlantillasDocumento"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la plantilla de documento a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plantilla de documento desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la plantilla de documento"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}