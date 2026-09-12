<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\DAO\PlanPpaDAO;
use App\DAO\ProyectoDAO;
use App\DAO\ClienteDAO;
use App\DAO\SedeDAO;
use App\DAO\PlantillaDocumentoDAO;
use App\DAO\PlantillaSeccionDAO;
use App\DAO\PlantillaVariableDAO;
use App\DAO\VariablePlantillaDAO;
use App\DAO\DtlleVariablePlantillaDAO;
use App\DAO\ConfiguracionEmpresaDAO;
use Mpdf\Mpdf;

class PropuestaPdfController {
    private $planPpaDao;
    private $proyectoDao;
    private $clienteDao;
    private $sedeDao;
    private $plantillaDocDao;
    private $plantillaSeccionDao;
    private $variablePlantillaDao;
    private $dtlleVariablePlantillaDao;
    private $configEmpresaDao;

    public function __construct() {
        $this->planPpaDao = new PlanPpaDAO();
        $this->proyectoDao = new ProyectoDAO();
        $this->clienteDao = new ClienteDAO();
        $this->sedeDao = new SedeDAO();
        $this->plantillaDocDao = new PlantillaDocumentoDAO();
        $this->plantillaSeccionDao = new PlantillaSeccionDAO();
        $this->plantillaVariableDao = new PlantillaVariableDAO();
        $this->variablePlantillaDao = new VariablePlantillaDAO();
        $this->dtlleVariablePlantillaDao = new DtlleVariablePlantillaDAO();
        $this->configEmpresaDao = new ConfiguracionEmpresaDAO();
    }

    public function generarPropuesta(Request $req, Response $res, array $args) {
        $planId = (int)$args['id'];

        $plan = $this->planPpaDao->getById($planId);
        if (!$plan) {
            return $res->withJson(['status' => 'error', 'message' => 'Plan PPA no encontrado'], 404);
        }

        $proyecto = $plan['proyectoId'] ? $this->proyectoDao->getById($plan['proyectoId']) : null;
        $cliente = $proyecto && $proyecto['clienteId'] ? $this->clienteDao->getById($proyecto['clienteId']) : null;
        $sede = $proyecto && $proyecto['sedeId'] ? $this->sedeDao->getById($proyecto['sedeId']) : null;

        $empresas = $this->configEmpresaDao->getAll();
        $empresa = $empresas[0] ?? null;

        $plantilla = null;
        if (!empty($plan['plantillaId'])) {
            $plantilla = $this->plantillaDocDao->getById($plan['plantillaId']);
        }
        if (!$plantilla) {
            $plantilla = $this->plantillaDocDao->getActiva();
        }
        if (!$plantilla) {
            return $res->withJson(['status' => 'error', 'message' => 'No hay plantilla configurada para este plan PPA'], 404);
        }

        $secciones = $this->plantillaSeccionDao->getByPlantillaId($plantilla['id']);

        $variables = $this->variablePlantillaDao->getAll();
        $detalles = [];
        $maestro = null;
        foreach ($variables as $v) {
            if (isset($v['planPpaId']) && (int)$v['planPpaId'] === $planId && (int)$v['anoProyeccion'] === 1) {
                $maestro = $v;
                $detalles = $this->dtlleVariablePlantillaDao->getByVplCont($v['id']);
                break;
            }
        }

        $valores = $this->construirDiccionarioValores($plan, $proyecto, $cliente, $sede, $empresa, $maestro, $detalles);
        $html = $this->construirHtml($plantilla, $secciones, $valores, $empresa);

        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_left' => 0, 'margin_right' => 0, 'margin_top' => 0, 'margin_bottom' => 0]);
            $mpdf->SetAuthor($empresa['nombre'] ?? 'Enersolax');
            $mpdf->SetTitle($plan['nombre'] ?? 'Propuesta PPA');
            $mpdf->WriteHTML($html);
            $pdfOutput = $mpdf->Output('', 'S');

            $res = $res->withHeader('Content-Type', 'application/pdf')
                       ->withHeader('Content-Disposition', 'inline; filename="propuesta_' . $planId . '.pdf"');
            $res->getBody()->write($pdfOutput);
            return $res;
        } catch (\Exception $e) {
            return $res->withJson(['status' => 'error', 'message' => 'Error generando PDF: ' . $e->getMessage()], 500);
        }
    }

    private function construirDiccionarioValores($plan, $proyecto, $cliente, $sede, $empresa, $maestro, $detalles) {
        $v = [];

        $v['EMPRESA_NOMBRE'] = $empresa['nombre'] ?? 'Enersolax';
        $v['EMPRESA_NIT'] = $empresa['nit'] ?? '';
        $v['EMPRESA_DIRECCION'] = $empresa['direccion'] ?? '';
        $v['EMPRESA_TELEFONO'] = $empresa['telefono'] ?? '';
        $v['EMPRESA_EMAIL'] = $empresa['email'] ?? '';
        $v['EMPRESA_SITIO_WEB'] = $empresa['sitioWeb'] ?? '';
        $v['EMPRESA_DESCRIPCION'] = $empresa['descripcion'] ?? '';
        $v['EMPRESA_LOGO'] = $this->cargarLogoSvg();

        $v['PLAN_NOMBRE'] = $plan['nombre'] ?? '';
        $v['PLAN_CODIGO'] = $plan['codigo'] ?? '';
        $v['PLAN_DURACION'] = ($plan['duracionAnos'] ?? '') . ' años';
        $v['PLAN_DESCUENTO'] = ($plan['descuento'] ?? '0') . '%';
        $v['PLAN_INVERSION'] = $this->formatoMoneda($plan['inversion'] ?? 0);
        $v['PLAN_INCLUYE_OM'] = ($plan['incluyeOM'] ?? '') === 'S' ? 'Sí' : 'No';
        $v['PLAN_INCLUYE_RETIE'] = ($plan['incluyeRetie'] ?? '') === 'S' ? 'Sí' : 'No';
        $v['PLAN_INCLUYE_LEGALIZACION'] = ($plan['incluyeLegalizacion'] ?? '') === 'S' ? 'Sí' : 'No';

        $v['PROYECTO_NOMBRE'] = $proyecto['nombre'] ?? '';
        $v['PROYECTO_CODIGO'] = $proyecto['codigo'] ?? '';
        $v['PROYECTO_ESTADO'] = $proyecto['estado'] ?? '';

        $v['CLIENTE_NOMBRE'] = $cliente['nombreCompleto'] ?? ($cliente['nombre'] ?? '');
        $v['CLIENTE_DOCUMENTO'] = $cliente['documento'] ?? '';
        $v['CLIENTE_DIRECCION'] = $cliente['direccion'] ?? '';
        $v['CLIENTE_CIUDAD'] = $cliente['ciudad'] ?? '';
        $v['CLIENTE_TELEFONO'] = $cliente['telefono'] ?? '';
        $v['CLIENTE_EMAIL'] = $cliente['email'] ?? '';

        $v['SEDE_NOMBRE'] = $sede['nombre'] ?? '';
        $v['SEDE_DIRECCION'] = $sede['direccion'] ?? '';
        $v['SEDE_CIUDAD'] = $sede['ciudad'] ?? '';
        $v['SEDE_AREA'] = $sede['area'] ?? '';

        $v['TARIFA_CONVENCIONAL'] = $this->formatoNumero($maestro['tarifaConvencional'] ?? 0, 4);
        $v['CONSUMO_ENERGIA'] = $this->formatoNumero($maestro['consumoEnergia'] ?? 0);
        $v['GENERACION_ENERGIA'] = $this->formatoNumero($maestro['generacionEnergia'] ?? 0);
        $v['COSTO_RED_REMANENTE'] = $this->formatoMoneda($maestro['costoRedRemanente'] ?? 0);

        $totalAhorro = 0;
        $totalConsumo = 0;
        $totalGeneracion = 0;
        $tablaProyeccion = '';

        if (!empty($detalles)) {
            $tablaProyeccion = '<table class="tabla-proyeccion">
                <tr>
                    <th>Año</th>
                    <th>Tarifa convencional</th>
                    <th>Tarifa PPA</th>
                    <th>Consumo GWh</th>
                    <th>Generación GWh</th>
                    <th>Ahorro</th>
                </tr>';

            foreach ($detalles as $d) {
                $totalAhorro += (float)($d['ahorroMillones'] ?? 0);
                $totalConsumo += (float)($d['consumoEnergia'] ?? 0);
                $totalGeneracion += (float)($d['generacionEnergia'] ?? 0);

                $tablaProyeccion .= '<tr>';
                $tablaProyeccion .= '<td>' . ($d['anoProyeccion'] ?? '') . '</td>';
                $tablaProyeccion .= '<td>' . $this->formatoNumero($d['tarifaConvencional'] ?? 0, 4) . '</td>';
                $tablaProyeccion .= '<td>' . $this->formatoNumero($d['tarifaPpa'] ?? 0, 4) . '</td>';
                $tablaProyeccion .= '<td>' . $this->formatoNumero($d['consumoEnergia'] ?? 0) . '</td>';
                $tablaProyeccion .= '<td>' . $this->formatoNumero($d['generacionEnergia'] ?? 0) . '</td>';
                $tablaProyeccion .= '<td>' . $this->formatoMoneda($d['ahorroMillones'] ?? 0) . '</td>';
                $tablaProyeccion .= '</tr>';
            }

            $tablaProyeccion .= '</table>';
        }

        $v['TABLA_PROYECCION'] = $tablaProyeccion;
        $v['TOTAL_AHORRO'] = $this->formatoMoneda($totalAhorro);
        $v['TOTAL_CONSUMO'] = $this->formatoNumero($totalConsumo);
        $v['TOTAL_GENERACION'] = $this->formatoNumero($totalGeneracion);
        $v['TOTAL_CO2'] = $this->formatoNumero($totalGeneracion * 1000 * 0.493, 2);
        $v['TOTAL_ARBOLES'] = $this->formatoNumero($totalGeneracion * 1000 * 0.493 * 7, 0);

        $v['FECHA_ACTUAL'] = $this->fechaLarga(date('Y-m-d'));
        $v['ANIO_ACTUAL'] = date('Y');

        return $v;
    }

    private function construirHtml($plantilla, $secciones, $valores, $empresa) {
        $imagenFondo = $this->rutaImagenPortada();
        $logo = $valores['EMPRESA_LOGO'];

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; color: #333; }
        .portada { height: 210mm; width: 297mm; border-collapse: collapse; }
        .portada td { padding: 0; vertical-align: top; }
        .portada-izq { width: 42%; background-color: #000000; color: #ffffff; padding: 50px 35px !important; }
        .portada-der { width: 58%; position: relative; background-color: #1a1a1a; }
        .portada-der img.fondo { width: 100%; height: 210mm; object-fit: cover; display: block; }
        .portada-logo { position: absolute; top: 25px; right: 25px; width: 140px; }
        .oferta { font-size: 11px; letter-spacing: 1px; margin-bottom: 70px; }
        .titulo { font-size: 34px; font-weight: bold; line-height: 1.1; margin: 0 0 35px 0; text-transform: uppercase; }
        .subtitulo { font-size: 20px; font-weight: bold; margin: 0 0 15px 0; }
        .tipo { font-size: 20px; font-weight: 300; margin: 0; }
        .contenido-pagina { padding: 40px; }
        .contenido-pagina h2 { font-size: 18px; color: #1a4a7a; border-bottom: 2px solid #1a4a7a; padding-bottom: 8px; margin-top: 0; }
        .contenido { text-align: justify; font-size: 11px; line-height: 1.5; }
        .tabla-proyeccion { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
        .tabla-proyeccion th { background-color: #1a4a7a; color: #fff; padding: 6px; text-align: right; }
        .tabla-proyeccion td { border: 1px solid #ccc; padding: 5px; text-align: right; }
        .tabla-proyeccion td:first-child, .tabla-proyeccion th:first-child { text-align: center; }
        .resumen { margin-top: 20px; padding: 15px; background: #f2f7fb; border-left: 5px solid #1a4a7a; }
        .resumen p { margin: 3px 0; }
        .pie { text-align: center; font-size: 9px; color: #777; margin-top: 40px; }
    </style>
</head>
<body>';

        // Portada
        $html .= '<table class="portada">';
        $html .= '<tr>';
        $html .= '<td class="portada-izq">';
        $html .= '<div class="oferta">Oferta ' . $valores['PROYECTO_CODIGO'] . '</div>';
        $html .= '<h1 class="titulo">' . mb_strtoupper($valores['PROYECTO_NOMBRE'], 'UTF-8') . '</h1>';
        $html .= '<div class="subtitulo">PPA</div>';
        $html .= '<div class="tipo">Energía Solar Fotovoltaica</div>';
        $html .= '</td>';
        $html .= '<td class="portada-der">';
        $html .= $logo;
        if ($imagenFondo) {
            $html .= '<img class="fondo" src="' . $imagenFondo . '" alt="" />';
        }
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Resumen
        $html .= '<div class="contenido-pagina" style="page-break-after: always;">';
        $html .= '<h2>Resumen ejecutivo</h2>';
        $html .= '<div class="contenido">';
        $html .= '<p>Propuesta comercial para el suministro de energía solar bajo el esquema PPA.</p>';
        $html .= '<div class="resumen">';
        $html .= '<p><strong>Ahorro total estimado:</strong> ' . $valores['TOTAL_AHORRO'] . '</p>';
        $html .= '<p><strong>Consumo anual:</strong> ' . $valores['TOTAL_CONSUMO'] . ' GWh</p>';
        $html .= '<p><strong>Generación anual:</strong> ' . $valores['TOTAL_GENERACION'] . ' GWh</p>';
        $html .= '<p><strong>Reducción de CO2:</strong> ' . $valores['TOTAL_CO2'] . ' t</p>';
        $html .= '</div>';
        $html .= $valores['TABLA_PROYECCION'];
        $html .= '</div>';
        $html .= '</div>';

        // Secciones de la plantilla
        foreach ($secciones as $seccion) {
            $contenido = $seccion['contenido'] ?? '';
            $contenido = $this->reemplazarVariables($contenido, $valores);

            $html .= '<div class="contenido-pagina" style="page-break-after: always;">';
            $html .= '<h2>' . ($seccion['titulo'] ?? $seccion['codigo']) . '</h2>';
            $html .= '<div class="contenido">' . $contenido . '</div>';
            $html .= '<div class="pie">' . $valores['EMPRESA_NOMBRE'] . ' · ' . $valores['EMPRESA_SITIO_WEB'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</body></html>';
        return $html;
    }

    private function reemplazarVariables($contenido, $valores) {
        return preg_replace_callback('/\{\{(\w+)\}\}/', function ($matches) use ($valores) {
            $clave = strtoupper($matches[1]);
            return isset($valores[$clave]) ? $valores[$clave] : $matches[0];
        }, $contenido);
    }

    private function cargarLogoSvg() {
        $rutas = [
            __DIR__ . '/../../assets/logo.svg',
            __DIR__ . '/../../assets/logo.png',
            __DIR__ . '/../../assets/logo.jpg',
            __DIR__ . '/../../../../app/src/assets/icons/logo.svg',
            __DIR__ . '/../../../../app/src/assets/icons/Logo-1.svg'
        ];

        foreach ($rutas as $ruta) {
            if (!file_exists($ruta)) continue;
            $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
            if ($ext === 'svg') {
                $svg = file_get_contents($ruta);
                return '<div class="portada-logo">' . $svg . '</div>';
            }
            return '<img class="portada-logo" src="' . $ruta . '" alt="logo" />';
        }

        return '';
    }

    private function rutaImagenPortada() {
        $rutasApi = [
            __DIR__ . '/../../assets/portada.jpg',
            __DIR__ . '/../../assets/portada.png',
            __DIR__ . '/../../assets/portada.webp'
        ];
        $rutasApp = [
            __DIR__ . '/../../../../app/src/assets/portada.jpg',
            __DIR__ . '/../../../../app/src/assets/portada.png',
            __DIR__ . '/../../../../app/src/assets/portada.webp'
        ];

        foreach (array_merge($rutasApi, $rutasApp) as $ruta) {
            if (file_exists($ruta)) return $ruta;
        }

        return '';
    }

    private function formatoMoneda($valor) {
        return '$' . number_format((float)$valor, 2, ',', '.');
    }

    private function formatoNumero($valor, $decimales = 2) {
        return number_format((float)$valor, $decimales, ',', '.');
    }

    private function fechaLarga($fecha) {
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $ts = strtotime($fecha);
        return date('d', $ts) . ' de ' . $meses[(int)date('m', $ts) - 1] . ' de ' . date('Y', $ts);
    }
}
