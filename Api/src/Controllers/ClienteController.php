<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\ClienteBO;
use App\TO\ClienteTO;

class ClienteController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new ClienteBO(); 
    }

    /**
     * @OA\Get(
     *     path="/clientes",
     *     summary="Obtener todos los clientes activos",
     *     tags={"Clientes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClienteTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/clientes/{id}",
     *     summary="Obtener cliente por ID",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ClienteTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Cliente no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/clientes",
     *     summary="Crear un nuevo cliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un cliente",
     *         @OA\JsonContent(ref="#/components/schemas/ClienteTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty(trim($data['nombreCompleto'] ?? '')) || empty(trim($data['tipo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El nombre completo y el tipo de cliente son obligatorios'], 400);
        }

        $clienteTO = new ClienteTO();
        $clienteTO->setDocumento($data['documento'] ?? null);
        $clienteTO->setNombreCompleto(trim($data['nombreCompleto']));
        $clienteTO->setTipo(trim($data['tipo']));
        $clienteTO->setDireccion($data['direccion'] ?? null);
        $clienteTO->setCiudad($data['ciudad'] ?? null);
        $clienteTO->setDepartamento($data['departamento'] ?? null);
        $clienteTO->setTelefono($data['telefono'] ?? null);
        $clienteTO->setEmail($data['email'] ?? null);
        $clienteTO->setEstado($data['estado'] ?? 'A');

        $resultado = $this->bo->create($clienteTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/clientes/{id}",
     *     summary="Actualizar un cliente existente",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cliente a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del cliente",
     *         @OA\JsonContent(ref="#/components/schemas/ClienteTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar cliente"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $clienteTO = new ClienteTO();

        // Mapeo dinámico
        if (isset($data['documento'])) $clienteTO->setDocumento(trim($data['documento']));
        if (isset($data['nombreCompleto'])) $clienteTO->setNombreCompleto(trim($data['nombreCompleto']));
        if (isset($data['tipo'])) $clienteTO->setTipo(trim($data['tipo']));
        if (isset($data['direccion'])) $clienteTO->setDireccion(trim($data['direccion']));
        if (isset($data['ciudad'])) $clienteTO->setCiudad(trim($data['ciudad']));
        if (isset($data['departamento'])) $clienteTO->setDepartamento(trim($data['departamento']));
        if (isset($data['telefono'])) $clienteTO->setTelefono(trim($data['telefono']));
        if (isset($data['email'])) $clienteTO->setEmail(trim($data['email']));
        if (isset($data['estado'])) $clienteTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($clienteTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/clientes/{id}",
     *     summary="Desactivar (borrado lógico) cliente por ID",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cliente a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el cliente"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}