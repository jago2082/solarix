<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\ProyectoBO;
use App\TO\ProyectoTO;

class ProyectoController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new ProyectoBO(); 
    }

    /**
     * @OA\Get(
     *     path="/proyectos",
     *     summary="Obtener todos los proyectos activos",
     *     tags={"Proyectos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de proyectos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProyectoTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/proyectos/{id}",
     *     summary="Obtener proyecto por ID",
     *     tags={"Proyectos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del proyecto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proyecto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ProyectoTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Proyecto no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Proyecto no encontrado'], 404);
    }

    public function getEstados(Request $req, Response $res) {
        return $res->withJson($this->bo->getEstados(), 200);
    }

    /**
     * @OA\Post(
     *     path="/proyectos",
     *     summary="Crear un nuevo proyecto",
     *     tags={"Proyectos"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un proyecto",
     *         @OA\JsonContent(ref="#/components/schemas/ProyectoTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Proyecto creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        // lInCli_cont y lStPro_codi son NOT NULL en la base de datos
        if (empty($data['clienteId']) || empty(trim($data['codigo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El ID del cliente y el código del proyecto son obligatorios'], 400);
        }

        $proyectoTO = new ProyectoTO();
        $proyectoTO->setClienteId($data['clienteId']);
        $proyectoTO->setSedeId($data['sedeId'] ?? null);
        $proyectoTO->setVisitaId($data['visitaId'] ?? null);
        $proyectoTO->setCodigo(trim($data['codigo']));
        $proyectoTO->setNombre(isset($data['nombre']) ? trim($data['nombre']) : null);
        $proyectoTO->setEstado($data['estado'] ?? 'PROSPECTO');

        $resultado = $this->bo->create($proyectoTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/proyectos/{id}",
     *     summary="Actualizar un proyecto existente",
     *     tags={"Proyectos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del proyecto a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del proyecto",
     *         @OA\JsonContent(ref="#/components/schemas/ProyectoTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proyecto actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar el proyecto"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $proyectoTO = new ProyectoTO();

        // Mapeo dinámico
        if (isset($data['clienteId'])) $proyectoTO->setClienteId($data['clienteId']);
        if (isset($data['sedeId'])) $proyectoTO->setSedeId($data['sedeId']);
        if (isset($data['visitaId'])) $proyectoTO->setVisitaId($data['visitaId']);
        if (isset($data['codigo'])) $proyectoTO->setCodigo(trim($data['codigo']));
        if (isset($data['nombre'])) $proyectoTO->setNombre(trim($data['nombre']));
        if (isset($data['estado'])) $proyectoTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($proyectoTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/proyectos/{id}",
     *     summary="Desactivar (borrado lógico) proyecto por ID",
     *     tags={"Proyectos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del proyecto a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proyecto desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el proyecto"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}