<?php
namespace App\Controllers;
require_once __DIR__ . '/../BO/DtlleVariablePlantillaBO.php';

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\DtlleVariablePlantillaBO;
use App\TO\DtlleVariablePlantillaTO;

class DtlleVariablePlantillaController {
    private $bo;

    public function __construct() {
        $this->bo = new DtlleVariablePlantillaBO();
    }

    /**
     * @OA\Get(
     *     path="/dtlle-Variables-plantilla",
     *     summary="Obtener todos los detalles de variables de plantilla",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de detalles obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DtlleVariablePlantillaTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/dtlle-Variables-plantilla/{id}",
     *     summary="Obtener detalle por ID",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/DtlleVariablePlantillaTO")
     *     ),
     *     @OA\Response(response=404, description="Detalle no encontrado")
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Detalle no encontrado'], 404);
    }

    /**
     * @OA\Get(
     *     path="/dtlle-Variables-plantilla/por-vpl/{vplCont}",
     *     summary="Obtener detalles por ID del encabezado",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Parameter(
     *         name="vplCont", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de detalles del encabezado",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DtlleVariablePlantillaTO")
     *         )
     *     )
     * )
     */
    public function getByVplCont(Request $req, Response $res, array $args) {
        return $res->withJson($this->bo->getByVplCont($args['vplCont']), 200);
    }

    /**
     * @OA\Post(
     *     path="/dtlle-Variables-plantilla",
     *     summary="Crear un detalle de variable de plantilla",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DtlleVariablePlantillaTO")
     *     ),
     *     @OA\Response(response=201, description="Detalle creado exitosamente"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty($data['vplCont']) || empty($data['anoProyeccion'])) {
            return $res->withJson(['status' => 'error', 'message' => 'El encabezado y el año de proyección son obligatorios'], 400);
        }

        $detalleTO = new DtlleVariablePlantillaTO();
        $detalleTO->setVplCont((int)$data['vplCont']);
        $detalleTO->setAnoProyeccion((int)$data['anoProyeccion']);
        $detalleTO->setTarifaConvencional(isset($data['tarifaConvencional']) ? (float)$data['tarifaConvencional'] : 0);
        $detalleTO->setTarifaPpa(isset($data['tarifaPpa']) ? (float)$data['tarifaPpa'] : 0);
        $detalleTO->setConsumoEnergia(isset($data['consumoEnergia']) ? (float)$data['consumoEnergia'] : 0);
        $detalleTO->setGeneracionEnergia(isset($data['generacionEnergia']) ? (float)$data['generacionEnergia'] : 0);
        $detalleTO->setCostoConsumoSinSsfv(isset($data['costoConsumoSinSsfv']) ? (float)$data['costoConsumoSinSsfv'] : 0);
        $detalleTO->setCostoRedRemanente(isset($data['costoRedRemanente']) ? (float)$data['costoRedRemanente'] : 0);
        $detalleTO->setCostoSsfvPpa(isset($data['costoSsfvPpa']) ? (float)$data['costoSsfvPpa'] : 0);
        $detalleTO->setCostoSsfvCostoRed(isset($data['costoSsfvCostoRed']) ? (float)$data['costoSsfvCostoRed'] : 0);
        $detalleTO->setAhorroMillones(isset($data['ahorroMillones']) ? (float)$data['ahorroMillones'] : 0);

        $resultado = $this->bo->create($detalleTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/dtlle-Variables-plantilla/{id}",
     *     summary="Actualizar un detalle",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DtlleVariablePlantillaTO")
     *     ),
     *     @OA\Response(response=200, description="Detalle actualizado exitosamente"),
     *     @OA\Response(response=400, description="Error al actualizar")
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $detalleTO = new DtlleVariablePlantillaTO();

        if (isset($data['vplCont'])) $detalleTO->setVplCont((int)$data['vplCont']);
        if (isset($data['anoProyeccion'])) $detalleTO->setAnoProyeccion((int)$data['anoProyeccion']);
        if (isset($data['tarifaConvencional'])) $detalleTO->setTarifaConvencional((float)$data['tarifaConvencional']);
        if (isset($data['tarifaPpa'])) $detalleTO->setTarifaPpa((float)$data['tarifaPpa']);
        if (isset($data['consumoEnergia'])) $detalleTO->setConsumoEnergia((float)$data['consumoEnergia']);
        if (isset($data['generacionEnergia'])) $detalleTO->setGeneracionEnergia((float)$data['generacionEnergia']);
        if (isset($data['costoConsumoSinSsfv'])) $detalleTO->setCostoConsumoSinSsfv((float)$data['costoConsumoSinSsfv']);
        if (isset($data['costoRedRemanente'])) $detalleTO->setCostoRedRemanente((float)$data['costoRedRemanente']);
        if (isset($data['costoSsfvPpa'])) $detalleTO->setCostoSsfvPpa((float)$data['costoSsfvPpa']);
        if (isset($data['costoSsfvCostoRed'])) $detalleTO->setCostoSsfvCostoRed((float)$data['costoSsfvCostoRed']);
        if (isset($data['ahorroMillones'])) $detalleTO->setAhorroMillones((float)$data['ahorroMillones']);

        $resultado = $this->bo->update($detalleTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/dtlle-Variables-plantilla/{id}",
     *     summary="Eliminar detalle por ID",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Detalle eliminado exitosamente"),
     *     @OA\Response(response=400, description="No se pudo eliminar el detalle")
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/dtlle-Variables-plantilla/por-vpl/{vplCont}",
     *     summary="Eliminar todos los detalles de un encabezado",
     *     tags={"DtlleVariablesPlantilla"},
     *     @OA\Parameter(
     *         name="vplCont", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Detalles eliminados exitosamente"),
     *     @OA\Response(response=400, description="Error al eliminar los detalles")
     * )
     */
    public function deleteByVplCont(Request $req, Response $res, array $args) {
        $resultado = $this->bo->deleteByVplCont($args['vplCont']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }
}
