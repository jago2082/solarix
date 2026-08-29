<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\ConfiguracionEmpresaBO;
use App\TO\ConfiguracionEmpresaTO;

class ConfiguracionEmpresaController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new ConfiguracionEmpresaBO(); 
    }

    /**
     * @OA\Get(
     *     path="/configuracion-empresa",
     *     summary="Obtener todas las configuraciones de empresa activas",
     *     tags={"ConfiguracionEmpresa"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de configuraciones de empresa obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ConfiguracionEmpresaTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/configuracion-empresa/{id}",
     *     summary="Obtener configuración de empresa por ID",
     *     tags={"ConfiguracionEmpresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la configuración de empresa",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Configuración de empresa encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ConfiguracionEmpresaTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Configuración no encontrada"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Configuración no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/configuracion-empresa",
     *     summary="Crear una nueva configuración de empresa",
     *     tags={"ConfiguracionEmpresa"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar la configuración de empresa",
     *         @OA\JsonContent(ref="#/components/schemas/ConfiguracionEmpresaTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Configuración creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty(trim($data['nombre'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El nombre de la empresa es obligatorio'], 400);
        }

        $configTO = new ConfiguracionEmpresaTO();
        $configTO->setNombre(trim($data['nombre']));
        $configTO->setNit(isset($data['nit']) ? trim($data['nit']) : null);
        $configTO->setDireccion(isset($data['direccion']) ? trim($data['direccion']) : null);
        $configTO->setTelefono(isset($data['telefono']) ? trim($data['telefono']) : null);
        $configTO->setEmail(isset($data['email']) ? trim($data['email']) : null);
        $configTO->setSitioWeb(isset($data['sitioWeb']) ? trim($data['sitioWeb']) : null);
        $configTO->setLogo(isset($data['logo']) ? trim($data['logo']) : null);
        $configTO->setDescripcion(isset($data['descripcion']) ? trim($data['descripcion']) : null);
        $configTO->setEstado(isset($data['estado']) ? trim($data['estado']) : 'A');

        $resultado = $this->bo->create($configTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/configuracion-empresa/{id}",
     *     summary="Actualizar una configuración de empresa existente",
     *     tags={"ConfiguracionEmpresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la configuración a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar de la configuración de empresa",
     *         @OA\JsonContent(ref="#/components/schemas/ConfiguracionEmpresaTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Configuración actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la configuración"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $configTO = new ConfiguracionEmpresaTO();

        // Mapeo dinámico
        if (isset($data['nombre'])) $configTO->setNombre(trim($data['nombre']));
        if (isset($data['nit'])) $configTO->setNit(trim($data['nit']));
        if (isset($data['direccion'])) $configTO->setDireccion(trim($data['direccion']));
        if (isset($data['telefono'])) $configTO->setTelefono(trim($data['telefono']));
        if (isset($data['email'])) $configTO->setEmail(trim($data['email']));
        if (isset($data['sitioWeb'])) $configTO->setSitioWeb(trim($data['sitioWeb']));
        if (isset($data['logo'])) $configTO->setLogo(trim($data['logo']));
        if (isset($data['descripcion'])) $configTO->setDescripcion(trim($data['descripcion']));
        if (isset($data['estado'])) $configTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($configTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/configuracion-empresa/{id}",
     *     summary="Desactivar (borrado lógico) configuración de empresa por ID",
     *     tags={"ConfiguracionEmpresa"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la configuración a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Configuración desactivada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar la configuración"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}