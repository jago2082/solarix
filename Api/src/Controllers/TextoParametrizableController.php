<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\BO\TextoParametrizableBO;
use App\TO\TextoParametrizableTO;

class TextoParametrizableController {
    private $bo;
    
    public function __construct() { 
        $this->bo = new TextoParametrizableBO(); 
    }

    /**
     * @OA\Get(
     *     path="/textos-parametrizables",
     *     summary="Obtener todos los textos parametrizables activos",
     *     tags={"TextosParametrizables"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de textos parametrizables obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TextoParametrizableTO")
     *         )
     *     )
     * )
     */
    public function getAll(Request $req, Response $res) {
        return $res->withJson($this->bo->getAll(), 200);
    }

    /**
     * @OA\Get(
     *     path="/textos-parametrizables/{id}",
     *     summary="Obtener texto parametrizable por ID",
     *     tags={"TextosParametrizables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del texto parametrizable",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Texto parametrizable encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/TextoParametrizableTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Texto parametrizable no encontrado"
     *     )
     * )
     */
    public function getById(Request $req, Response $res, array $args) {
        $data = $this->bo->getById($args['id']);
        if ($data) {
            return $res->withJson($data, 200);
        }
        return $res->withJson(['status' => 'error', 'message' => 'Texto parametrizable no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/textos-parametrizables",
     *     summary="Crear un nuevo texto parametrizable",
     *     tags={"TextosParametrizables"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para registrar un texto parametrizable",
     *         @OA\JsonContent(ref="#/components/schemas/TextoParametrizableTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Texto parametrizable creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos faltantes"
     *     )
     * )
     */
    public function create(Request $req, Response $res) {
        $data = $req->getParsedBody();

        if (empty(trim($data['codigo'] ?? ''))) {
            return $res->withJson(['status' => 'error', 'message' => 'El código del texto es obligatorio'], 400);
        }

        $textoTO = new TextoParametrizableTO();
        $textoTO->setCodigo(trim($data['codigo']));
        $textoTO->setTitulo(isset($data['titulo']) ? trim($data['titulo']) : null);
        $textoTO->setContenido(isset($data['contenido']) ? trim($data['contenido']) : null);
        $textoTO->setEstado(isset($data['estado']) ? (int)$data['estado'] : 1);

        $resultado = $this->bo->create($textoTO);
        $statusCode = ($resultado['status'] === 'success') ? 201 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Put(
     *     path="/textos-parametrizables/{id}",
     *     summary="Actualizar un texto parametrizable existente",
     *     tags={"TextosParametrizables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del texto parametrizable a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a actualizar del texto parametrizable",
     *         @OA\JsonContent(ref="#/components/schemas/TextoParametrizableTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Texto parametrizable actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar el texto parametrizable"
     *     )
     * )
     */
    public function update(Request $req, Response $res, array $args) {
        $data = $req->getParsedBody();
        $textoTO = new TextoParametrizableTO();

        // Mapeo dinámico
        if (isset($data['codigo'])) $textoTO->setCodigo(trim($data['codigo']));
        if (isset($data['titulo'])) $textoTO->setTitulo(trim($data['titulo']));
        if (isset($data['contenido'])) $textoTO->setContenido(trim($data['contenido']));
        if (isset($data['estado'])) $textoTO->setEstado((int)$data['estado']);

        $resultado = $this->bo->update($textoTO, $args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;

        return $res->withJson($resultado, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/textos-parametrizables/{id}",
     *     summary="Desactivar (borrado lógico) texto parametrizable por ID",
     *     tags={"TextosParametrizables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del texto parametrizable a desactivar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Texto parametrizable desactivado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se pudo desactivar el texto parametrizable"
     *     )
     * )
     */
    public function delete(Request $req, Response $res, array $args) {
        $resultado = $this->bo->delete($args['id']);
        $statusCode = ($resultado['status'] === 'success') ? 200 : 400;
        
        return $res->withJson($resultado, $statusCode);
    }
}