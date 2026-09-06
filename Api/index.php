<?php
// Ocultar avisos de deprecación producidos por Slim 3 en PHP 8+
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '1');

use OpenApi\Generator;
use Slim\Http\Request;
use Slim\Http\Response;

require 'vendor/autoload.php';

$config = ['settings' => ['displayErrorDetails' => true]];
$app = new \Slim\App($config);

// --- FIX PARA LITESPEED / SUBDOMINIOS ---
$container = $app->getContainer();
$container['environment'] = function () {
    $server = $_SERVER;
    $server['SCRIPT_NAME'] = '/index.php'; 
    
    $uri = $server['REQUEST_URI'];
    
    // Lista de tus módulos (rutas que necesitan el /api)
    $apiModules = [
        '/usuarios', '/roles', '/usuario-roles', '/sesiones', '/clientes', 
        '/contactos', '/sedes', '/visitas', '/proyectos', '/planes-ppa', 
        '/variables-plantilla', '/textos-parametrizables', '/configuracion-empresa', 
        '/plantillas-documento', '/plantilla-secciones', '/plantilla-variables'
    ];
    
    // Si la URI empieza con alguno de tus módulos, le reponemos el '/api' que borró LiteSpeed
    foreach ($apiModules as $module) {
        if (strpos($uri, $module) === 0) {
            $server['REQUEST_URI'] = '/api' . $uri;
            break;
        }
    }
    
    return new \Slim\Http\Environment($server);
};

// Agregamos una ruta raíz por si alguien entra al dominio directamente sin nada
$app->get('/', function ($request, $response) {
    return $response->withRedirect('/docs'); // Te enviará directo al Swagger
});

// --- RUTAS GENERADAS ---
$app->group('/api/usuarios', function () use ($app) {
    // LOGIN MOVIDO AQUÍ PARA QUE LA RUTA SEA /api/usuarios/login
    $app->post('/login', \App\Controllers\UsuarioController::class . ':login');
    
    $app->get('', \App\Controllers\UsuarioController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\UsuarioController::class . ':getById');
    $app->post('', \App\Controllers\UsuarioController::class . ':create');
    $app->put('/{id}[/]', \App\Controllers\UsuarioController::class . ':update');
    $app->delete('/{id}', \App\Controllers\UsuarioController::class . ':delete');
});

$app->group('/api/roles', function () use ($app) {
    $app->get('', \App\Controllers\RolController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\RolController::class . ':getById');
    $app->post('', \App\Controllers\RolController::class . ':create');
    $app->put('/{id}', \App\Controllers\RolController::class . ':update');
    $app->delete('/{id}', \App\Controllers\RolController::class . ':delete');
});

$app->group('/api/usuario-roles', function () use ($app) {
    $app->get('', \App\Controllers\UsuarioRolController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\UsuarioRolController::class . ':getById');
    $app->post('', \App\Controllers\UsuarioRolController::class . ':create');
    $app->put('/{id}', \App\Controllers\UsuarioRolController::class . ':update');
    $app->delete('/{id}', \App\Controllers\UsuarioRolController::class . ':delete');
});

$app->group('/api/sesiones', function () use ($app) {
    $app->get('', \App\Controllers\SesionController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\SesionController::class . ':getById');
    $app->post('', \App\Controllers\SesionController::class . ':create');
    $app->put('/{id}', \App\Controllers\SesionController::class . ':update');
    $app->delete('/{id}', \App\Controllers\SesionController::class . ':delete');
});

$app->group('/api/clientes', function () use ($app) {
    $app->get('', \App\Controllers\ClienteController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\ClienteController::class . ':getById');
    $app->post('', \App\Controllers\ClienteController::class . ':create');
    $app->put('/{id}', \App\Controllers\ClienteController::class . ':update');
    $app->delete('/{id}', \App\Controllers\ClienteController::class . ':delete');
});

$app->group('/api/contactos', function () use ($app) {
    $app->get('', \App\Controllers\ContactoController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\ContactoController::class . ':getById');
    $app->post('', \App\Controllers\ContactoController::class . ':create');
    $app->put('/{id}', \App\Controllers\ContactoController::class . ':update');
    $app->delete('/{id}', \App\Controllers\ContactoController::class . ':delete');
});

$app->group('/api/sedes', function () use ($app) {
    $app->get('', \App\Controllers\SedeController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\SedeController::class . ':getById');
    $app->post('', \App\Controllers\SedeController::class . ':create');
    $app->put('/{id}', \App\Controllers\SedeController::class . ':update');
    $app->delete('/{id}', \App\Controllers\SedeController::class . ':delete');
});

$app->group('/api/visitas', function () use ($app) {
    $app->get('', \App\Controllers\VisitaController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\VisitaController::class . ':getById');
    $app->post('', \App\Controllers\VisitaController::class . ':create');
    $app->put('/{id}', \App\Controllers\VisitaController::class . ':update');
    $app->delete('/{id}', \App\Controllers\VisitaController::class . ':delete');
});

$app->group('/api/proyectos', function () use ($app) {
    $app->get('', \App\Controllers\ProyectoController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\ProyectoController::class . ':getById');
    $app->post('', \App\Controllers\ProyectoController::class . ':create');
    $app->put('/{id}', \App\Controllers\ProyectoController::class . ':update');
    $app->delete('/{id}', \App\Controllers\ProyectoController::class . ':delete');
});

$app->group('/api/planes-ppa', function () use ($app) {
    $app->get('', \App\Controllers\PlanPpaController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\PlanPpaController::class . ':getById');
    $app->post('', \App\Controllers\PlanPpaController::class . ':create');
    $app->put('/{id}', \App\Controllers\PlanPpaController::class . ':update');
    $app->delete('/{id}', \App\Controllers\PlanPpaController::class . ':delete');
});

$app->group('/api/variables-plantilla', function () use ($app) {
    $app->get('', \App\Controllers\VariablePlantillaController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\VariablePlantillaController::class . ':getById');
    $app->post('', \App\Controllers\VariablePlantillaController::class . ':create');
    $app->put('/{id}', \App\Controllers\VariablePlantillaController::class . ':update');
    $app->delete('/{id}', \App\Controllers\VariablePlantillaController::class . ':delete');
});

$app->group('/api/textos-parametrizables', function () use ($app) {
    $app->get('', \App\Controllers\TextoParametrizableController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\TextoParametrizableController::class . ':getById');
    $app->post('', \App\Controllers\TextoParametrizableController::class . ':create');
    $app->put('/{id}', \App\Controllers\TextoParametrizableController::class . ':update');
    $app->delete('/{id}', \App\Controllers\TextoParametrizableController::class . ':delete');
});

$app->group('/api/configuracion-empresa', function () use ($app) {
    $app->get('', \App\Controllers\ConfiguracionEmpresaController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\ConfiguracionEmpresaController::class . ':getById');
    $app->post('', \App\Controllers\ConfiguracionEmpresaController::class . ':create');
    $app->put('/{id}', \App\Controllers\ConfiguracionEmpresaController::class . ':update');
    $app->delete('/{id}', \App\Controllers\ConfiguracionEmpresaController::class . ':delete');
});

$app->group('/api/plantillas-documento', function () use ($app) {
    $app->get('', \App\Controllers\PlantillaDocumentoController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\PlantillaDocumentoController::class . ':getById');
    $app->post('', \App\Controllers\PlantillaDocumentoController::class . ':create');
    $app->put('/{id}', \App\Controllers\PlantillaDocumentoController::class . ':update');
    $app->delete('/{id}', \App\Controllers\PlantillaDocumentoController::class . ':delete');
});

$app->group('/api/plantilla-secciones', function () use ($app) {
    $app->get('', \App\Controllers\PlantillaSeccionController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\PlantillaSeccionController::class . ':getById');
    $app->post('', \App\Controllers\PlantillaSeccionController::class . ':create');
    $app->put('/{id}', \App\Controllers\PlantillaSeccionController::class . ':update');
    $app->delete('/{id}', \App\Controllers\PlantillaSeccionController::class . ':delete');
});

$app->group('/api/plantilla-variables', function () use ($app) {
    $app->get('', \App\Controllers\PlantillaVariableController::class . ':getAll');
    $app->get('/{id}', \App\Controllers\PlantillaVariableController::class . ':getById');
    $app->post('', \App\Controllers\PlantillaVariableController::class . ':create');
    $app->put('/{id}', \App\Controllers\PlantillaVariableController::class . ':update');
    $app->delete('/{id}', \App\Controllers\PlantillaVariableController::class . ':delete');
});

$app->get('/hola', function ($request, $response) {
    return $response->withJson(['mensaje' => '¡Slim está enrutando perfectamente!']);
});


// --- CONFIGURACIÓN CORS ---

// 1. Atrapa todas las peticiones OPTIONS (Preflight)
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// 2. Middleware global para inyectar las cabeceras a todas las respuestas
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*') 
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, access-control-allow-origin, cache-control')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// --- FIN CONFIGURACIÓN CORS ---


// --- RUTAS DE DOCUMENTACIÓN SWAGGER ---

// 1. Endpoint que genera dinámicamente el JSON OpenAPI
$app->get('/docs/json', function (Request $request, Response $response) {
    $srcPath = realpath(__DIR__ . '/src') ?: realpath(__DIR__ . '/../src');
    
    $openapi = Generator::scan([$srcPath]);
    
    if (ob_get_length()) {
        ob_clean();
    }

    $response->getBody()->write($openapi->toJson());
    return $response->withHeader('Content-Type', 'application/json');
});

// 2. Endpoint que sirve la interfaz gráfica interactiva de Swagger UI
$app->get('/docs', function (Request $request, Response $response) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación API - Swagger UI</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: '/docs/json',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis
                ]
            });
        };
    </script>
</body>
</html>
HTML;

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

$app->run();