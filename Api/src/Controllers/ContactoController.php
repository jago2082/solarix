<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\ContactoBO;
use App\TO\ContactoTO;

class ContactoController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new ContactoBO(); 
    }

    /**
     * @OA\Get(
     *     path="/contactos",
     *     summary="Obtener todos los contactos activos",
     *     tags={"Contactos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de contactos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ContactoTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/contactos/{id}",
     *     summary="Obtener contacto por ID",
     *     tags={"Contactos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contacto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ContactoTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contacto no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Contacto no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/contactos",
     *     summary="Crear un nuevo contacto",
     *     tags={"Contactos"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un contacto",
     *         @OA\JsonContent(ref="#/components/schemas/ContactoTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contacto creado exitosamente"
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
            return $res->withJson(['status' => 'error', 'message' => 'El ID del cliente y el nombre son obligatorios'], 400);
        }

        $contactoTO = new ContactoTO();
        $contactoTO->setClienteId($data['clienteId']);
        $contactoTO->setNombre(trim($data['nombre']));
        $contactoTO->setCargo($data['cargo'] ?? null);
        $contactoTO->setTelefono($data['telefono'] ?? null);
        $contactoTO->setEmail($data['email'] ?? null);
        $contactoTO->setPrincipal(isset($data['principal']) ? (int)$data['principal'] : 0);
        $contactoTO->setEstado($data['estado'] ?? 'A');

        $resultado = $this->bo->create($contactoTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/contactos/{id}",
     *     summary="Actualizar un contacto existente",
     *     tags={"Contactos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del contacto a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del contacto",
     *         @OA\JsonContent(ref="#/components/schemas/ContactoTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contacto actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar contacto"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $contactoTO = new ContactoTO();

        // Mapeo dinámico
        if (isset($data['clienteId'])) $contactoTO->setClienteId($data['clienteId']);
        if (isset($data['nombre'])) $contactoTO->setNombre(trim($data['nombre']));
        if (isset($data['cargo'])) $contactoTO->setCargo(trim($data['cargo']));
        if (isset($data['telefono'])) $contactoTO->setTelefono(trim($data['telefono']));
        if (isset($data['email'])) $contactoTO->setEmail(trim($data['email']));
        if (isset($data['principal'])) $contactoTO->setPrincipal((int)$data['principal']);
        if (isset($data['estado'])) $contactoTO->setEstado(trim($data['estado']));

        $resultado = $this->bo->update($contactoTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/contactos/{id}",
     *     summary="Desactivar (borrado lógico) contacto por ID",
     *     tags={"Contactos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del contacto a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contacto desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el contacto"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}