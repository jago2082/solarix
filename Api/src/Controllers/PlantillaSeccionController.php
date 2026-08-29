<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\PlantillaSeccionBO;
use App\TO\PlantillaSeccionTO;

class PlantillaSeccionController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new PlantillaSeccionBO(); 
    }

    /**
     * @OA\Get(
     *     path="/plantilla-secciones",
     *     summary="Obtener todas las secciones de plantilla activas",
     *     tags={"PlantillaSecciones"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de secciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PlantillaSeccionTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/plantilla-secciones/{id}",
     *     summary="Obtener sección de plantilla por ID",
     *     tags={"PlantillaSecciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sección",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sección de plantilla encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaSeccionTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sección de plantilla no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Sección de plantilla no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/plantilla-secciones",
     *     summary="Crear una nueva sección de plantilla",
     *     tags={"PlantillaSecciones"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una sección de plantilla",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaSeccionTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sección de plantilla creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['plantillaId']) || empty(trim($data['codigo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El ID de la plantilla y el código de la sección son obligatorios'], 400);
        }

        $seccionTO = new PlantillaSeccionTO();
        $seccionTO->setPlantillaId((int)$data['plantillaId']);
        $seccionTO->setCodigo(trim($data['codigo']));
        $seccionTO->setTitulo(isset($data['titulo']) ? trim($data['titulo']) : null);
        $seccionTO->setOrden(isset($data['orden']) ? (int)$data['orden'] : 1);
        $seccionTO->setContenido(isset($data['contenido']) ? trim($data['contenido']) : null);
        $seccionTO->setEstado(isset($data['estado']) ? trim($data['estado']) : 'A');

        $resultado = $this->bo->create($seccionTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/plantilla-secciones/{id}",
     *     summary="Actualizar una sección de plantilla existente",
     *     tags={"PlantillaSecciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sección a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la sección de plantilla",
     *         @OA\JsonContent(ref="#/components/schemas/PlantillaSeccionTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sección de plantilla actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la sección de plantilla"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $seccionTO = new PlantillaSeccionTO();

        // Mapeo dinámico
        if (isset($data['plantillaId'])) $seccionTO->setPlantillaId((int)$data['plantillaId']);
        if (isset($data['codigo'])) $seccionTO->setCodigo(trim($data['codigo']));
        if (isset($data['titulo'])) $seccionTO->setTitulo(trim($data['titulo']));
        if (isset($data['orden'])) $seccionTO->setOrden((int)$data['orden']);
        if (isset($data['contenido'])) $seccionTO->setContenido(trim($data['contenido']));
        if (isset($data['estado'])) $seccionTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($seccionTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/plantilla-secciones/{id}",
     *     summary="Desactivar (borrado lógico) sección de plantilla por ID",
     *     tags={"PlantillaSecciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sección a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sección de plantilla desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la sección de plantilla"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}