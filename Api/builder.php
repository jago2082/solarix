<?php
// builder.php - Generador automático de arquitectura API REST Slim 3

$tables = [
    'usuarios' => ['id' => 'lInUsu_cont', 'entity' => 'Usuario'],
    'roles' => ['id' => 'lInRol_cont', 'entity' => 'Rol'],
    'usuario_roles' => ['id' => 'lInUro_cont', 'entity' => 'UsuarioRol'],
    'sesiones' => ['id' => 'lInSes_cont', 'entity' => 'Sesion'],
    'clientes' => ['id' => 'lInCli_cont', 'entity' => 'Cliente'],
    'contactos' => ['id' => 'lInCon_cont', 'entity' => 'Contacto'],
    'sedes' => ['id' => 'lInSed_cont', 'entity' => 'Sede'],
    'visitas' => ['id' => 'lInVis_cont', 'entity' => 'Visita'],
    'proyectos' => ['id' => 'lInPro_cont', 'entity' => 'Proyecto'],
    'planes_ppa' => ['id' => 'lInPpa_cont', 'entity' => 'PlanPpa'],
    'variables_plantilla' => ['id' => 'lInVpl_cont', 'entity' => 'VariablePlantilla'],
    'textos_parametrizables' => ['id' => 'lInTxp_cont', 'entity' => 'TextoParametrizable'],
    'configuracion_empresa' => ['id' => 'lInCfe_cont', 'entity' => 'ConfiguracionEmpresa'],
    'plantillas_documento' => ['id' => 'lInPtd_cont', 'entity' => 'PlantillaDocumento'],
    'plantilla_secciones' => ['id' => 'lInPse_cont', 'entity' => 'PlantillaSeccion'],
    'plantilla_variables' => ['id' => 'lInPva_cont', 'entity' => 'PlantillaVariable']
];

// 1. Crear estructura de carpetas
$directories = ['public', 'src/Config', 'src/Controllers', 'src/BO', 'src/DAO', 'src/TO'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// 2. Crear composer.json
$composer = <<<EOT
{
    "name": "empresa/api-ppa",
    "description": "API REST PPA construida con Slim 3",
    "require": {
        "slim/slim": "^3.12"
    },
    "autoload": {
        "psr-4": {
            "App\\\\": "src/"
        }
    }
}
EOT;
file_put_contents('composer.json', $composer);

// 3. Crear Database.php
$dbClass = <<<EOT
<?php
namespace App\Config;
use PDO;
use PDOException;

class Database {
    private static \$instance = null;
    private \$conn;

    private function __construct() {
        try {
            \$this->conn = new PDO("mysql:host=localhost;dbname=ppa_db;charset=utf8", "root", "");
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException \$e) {
            echo "Connection error: " . \$e->getMessage();
        }
    }

    public static function getInstance() {
        if (!self::\$instance) { self::\$instance = new Database(); }
        return self::\$instance;
    }

    public function getConnection() { return \$this->conn; }
}
EOT;
file_put_contents('src/Config/Database.php', $dbClass);

// 4. Generar Archivos para cada Tabla
$indexRoutes = "";

foreach ($tables as $table => $info) {
    $entity = $info['entity'];
    $pk = $info['id'];
    $route = str_replace('_', '-', $table);

    // -- TO --
    $toClass = "<?php\nnamespace App\\TO;\n\nclass {$entity}TO {\n    public \$datos = [];\n    public function __construct(\$data = []) {\n        \$this->datos = \$data;\n    }\n}";
    file_put_contents("src/TO/{$entity}TO.php", $toClass);

    // -- DAO -- (CRUD Dinámico por arreglos)
    $daoClass = <<<EOT
<?php
namespace App\DAO;
use App\Config\Database;
use App\TO\\{$entity}TO;
use PDO;

class {$entity}DAO {
    private \$conn;
    public function __construct() { \$this->conn = Database::getInstance()->getConnection(); }

    public function getAll() {
        \$stmt = \$this->conn->prepare("SELECT * FROM {$table}");
        \$stmt->execute();
        return \$stmt->fetchAll();
    }

    public function getById(\$id) {
        \$stmt = \$this->conn->prepare("SELECT * FROM {$table} WHERE {$pk} = :id");
        \$stmt->execute(['id' => \$id]);
        return \$stmt->fetch();
    }

    public function insert({$entity}TO \$obj) {
        \$fields = array_keys(\$obj->datos);
        \$cols = implode(', ', \$fields);
        \$places = ':' . implode(', :', \$fields);
        \$stmt = \$this->conn->prepare("INSERT INTO {$table} (\$cols) VALUES (\$places)");
        return \$stmt->execute(\$obj->datos);
    }

    public function update({$entity}TO \$obj, \$id) {
        \$set = '';
        foreach (\$obj->datos as \$key => \$val) { \$set .= "\$key = :\$key, "; }
        \$set = rtrim(\$set, ', ');
        \$stmt = \$this->conn->prepare("UPDATE {$table} SET \$set WHERE {$pk} = :_pk");
        \$datos = \$obj->datos;
        \$datos['_pk'] = \$id;
        return \$stmt->execute(\$datos);
    }

    public function delete(\$id) {
        \$stmt = \$this->conn->prepare("DELETE FROM {$table} WHERE {$pk} = :id");
        return \$stmt->execute(['id' => \$id]);
    }
}
EOT;
    file_put_contents("src/DAO/{$entity}DAO.php", $daoClass);

    // -- BO --
    $boClass = <<<EOT
<?php
namespace App\BO;
use App\DAO\\{$entity}DAO;
use App\TO\\{$entity}TO;

class {$entity}BO {
    private \$dao;
    public function __construct() { \$this->dao = new {$entity}DAO(); }
    public function getAll() { return \$this->dao->getAll(); }
    public function getById(\$id) { return \$this->dao->getById(\$id); }
    public function create(\$data) { return \$this->dao->insert(new {$entity}TO(\$data)); }
    public function update(\$data, \$id) { return \$this->dao->update(new {$entity}TO(\$data), \$id); }
    public function delete(\$id) { return \$this->dao->delete(\$id); }
}
EOT;
    file_put_contents("src/BO/{$entity}BO.php", $boClass);

    // -- CONTROLLER --
    $ctrlClass = <<<EOT
<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\BO\\{$entity}BO;

class {$entity}Controller {
    private \$bo;
    public function __construct() { \$this->bo = new {$entity}BO(); }

    public function getAll(Request \$req, Response \$res) {
        return \$res->withJson(\$this->bo->getAll(), 200);
    }

    public function getById(Request \$req, Response \$res, array \$args) {
        \$data = \$this->bo->getById(\$args['id']);
        return \$data ? \$res->withJson(\$data, 200) : \$res->withJson(['msg'=>'No encontrado'], 404);
    }

    public function create(Request \$req, Response \$res) {
        \$data = json_decode(\$req->getBody()->getContents(), true);
        \$this->bo->create(\$data);
        return \$res->withJson(['msg' => 'Creado exitosamente'], 201);
    }

    public function update(Request \$req, Response \$res, array \$args) {
        \$data = json_decode(\$req->getBody()->getContents(), true);
        \$this->bo->update(\$data, \$args['id']);
        return \$res->withJson(['msg' => 'Actualizado exitosamente'], 200);
    }

    public function delete(Request \$req, Response \$res, array \$args) {
        \$this->bo->delete(\$args['id']);
        return \$res->withJson(['msg' => 'Eliminado exitosamente'], 200);
    }
}
EOT;
    file_put_contents("src/Controllers/{$entity}Controller.php", $ctrlClass);

    // Concatenar rutas para el index.php
    $indexRoutes .= <<<EOT
\$app->group('/api/{$route}', function () use (\$app) {
    \$app->get('', \App\Controllers\\{$entity}Controller::class . ':getAll');
    \$app->get('/{id}', \App\Controllers\\{$entity}Controller::class . ':getById');
    \$app->post('', \App\Controllers\\{$entity}Controller::class . ':create');
    \$app->put('/{id}', \App\Controllers\\{$entity}Controller::class . ':update');
    \$app->delete('/{id}', \App\Controllers\\{$entity}Controller::class . ':delete');
});\n
EOT;
}

// 5. Crear public/index.php
$indexClass = <<<EOT
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

\$config = ['settings' => ['displayErrorDetails' => true]];
\$app = new \Slim\App(\$config);

// --- RUTAS GENERADAS ---
{$indexRoutes}
\$app->run();
EOT;
file_put_contents('public/index.php', $indexClass);

echo "¡Proyecto API PPA generado con éxito! Carpetas, BO, DAO, TO, Controladores e index.php listos.\n";
echo "No olvides ejecutar 'composer install' y configurar tus credenciales en src/Config/Database.php.\n";
?>