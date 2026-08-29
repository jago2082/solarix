<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\VariablePlantillaBO;
use App\TO\VariablePlantillaTO;

class VariablePlantillaController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new VariablePlantillaBO(); 
    }

    /**
     * @OA\Get(
     *     path="/variables-plantilla",
     *     summary="Obtener todas las variables de proyecciones PPA",
     *     tags={"VariablesPlantilla"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de variables de plantilla obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VariablePlantillaTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/variables-plantilla/{id}",
     *     summary="Obtener variable de plantilla por ID",
     *     tags={"VariablesPlantilla"},
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
     *         @OA\JsonContent(ref="#/components/schemas/VariablePlantillaTO")
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
     *     path="/variables-plantilla",
     *     summary="Crear una nueva variable de proyección de plantilla",
     *     tags={"VariablesPlantilla"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar una proyección de plantilla PPA",
     *         @OA\JsonContent(ref="#/components/schemas/VariablePlantillaTO")
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

        if (empty($data['planPpaId']) || empty($data['anoProyeccion'])) {
            return $res->withJson(['status' => 'error', 'message' => 'El plan PPA y el año de proyección son obligatorios'], 400);
        }

        $variableTO = new VariablePlantillaTO();
        $variableTO->setPlanPpaId((int)$data['planPpaId']);
        $variableTO->setAnoProyeccion((int)$data['anoProyeccion']);
        $variableTO->setTarifaConvencional(isset($data['tarifaConvencional']) ? (float)$data['tarifaConvencional'] : 0);
        $variableTO->setTarifaPpa(isset($data['tarifaPpa']) ? (float)$data['tarifaPpa'] : 0);
        $variableTO->setConsumoEnergia(isset($data['consumoEnergia']) ? (float)$data['consumoEnergia'] : 0);
        $variableTO->setGeneracionEnergia(isset($data['generacionEnergia']) ? (float)$data['generacionEnergia'] : 0);
        $variableTO->setCostoConsumoSinSsfv(isset($data['costoConsumoSinSsfv']) ? (float)$data['costoConsumoSinSsfv'] : 0);
        $variableTO->setCostoRedRemanente(isset($data['costoRedRemanente']) ? (float)$data['costoRedRemanente'] : 0);
        $variableTO->setCostoSsfvPpa(isset($data['costoSsfvPpa']) ? (float)$data['costoSsfvPpa'] : 0);
        $variableTO->setCostoSsfvCostoRed(isset($data['costoSsfvCostoRed']) ? (float)$data['costoSsfvCostoRed'] : 0);
        $variableTO->setAhorroMillones(isset($data['ahorroMillones']) ? (float)$data['ahorroMillones'] : 0);

        $resultado = $this->bo->create($variableTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/variables-plantilla/{id}",
     *     summary="Actualizar una variable de plantilla existente",
     *     tags={"VariablesPlantilla"},
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
     *         @OA\JsonContent(ref="#/components/schemas/VariablePlantillaTO")
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
        $variableTO = new VariablePlantillaTO();

        // Mapeo dinámico
        if (isset($data['planPpaId'])) $variableTO->setPlanPpaId((int)$data['planPpaId']);
        if (isset($data['anoProyeccion'])) $variableTO->setAnoProyeccion((int)$data['anoProyeccion']);
        if (isset($data['tarifaConvencional'])) $variableTO->setTarifaConvencional((float)$data['tarifaConvencional']);
        if (isset($data['tarifaPpa'])) $variableTO->setTarifaPpa((float)$data['tarifaPpa']);
        if (isset($data['consumoEnergia'])) $variableTO->setConsumoEnergia((float)$data['consumoEnergia']);
        if (isset($data['generacionEnergia'])) $variableTO->setGeneracionEnergia((float)$data['generacionEnergia']);
        if (isset($data['costoConsumoSinSsfv'])) $variableTO->setCostoConsumoSinSsfv((float)$data['costoConsumoSinSsfv']);
        if (isset($data['costoRedRemanente'])) $variableTO->setCostoRedRemanente((float)$data['costoRedRemanente']);
        if (isset($data['costoSsfvPpa'])) $variableTO->setCostoSsfvPpa((float)$data['costoSsfvPpa']);
        if (isset($data['costoSsfvCostoRed'])) $variableTO->setCostoSsfvCostoRed((float)$data['costoSsfvCostoRed']);
        if (isset($data['ahorroMillones'])) $variableTO->setAhorroMillones((float)$data['ahorroMillones']);

        $resultado = $this->bo->update($variableTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/variables-plantilla/{id}",
     *     summary="Eliminar variable de plantilla por ID",
     *     tags={"VariablesPlantilla"},
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