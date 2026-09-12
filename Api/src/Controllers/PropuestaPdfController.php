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
use Dompdf\Dompdf;
use Dompdf\Options;

class PropuestaPdfController {
    private $planPpaDao;
    private $proyectoDao;
    private $clienteDao;
    private $sedeDao;
    private $plantillaDocDao;
    private $plantillaSeccionDao;
    private $plantillaVariableDao;
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
        $modoDebug = $req->getQueryParam('debug') === 'html';
        $html = $this->construirHtml($plantilla, $secciones, $valores, $empresa, $modoDebug);

        if ($modoDebug) {
            $res = $res->withHeader('Content-Type', 'text/html; charset=utf-8');
            $res->getBody()->write($html);
            return $res;
        }

        try {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('defaultPaperSize', 'A4');

            $dompdf = new Dompdf($options);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();
            $pdfOutput = $dompdf->output();

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

    private function construirHtml($plantilla, $secciones, $valores, $empresa, $modoDebug = false) {
        $imagenFondo = $this->urlImagenPortada();
        $logo = $valores['EMPRESA_LOGO'];

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; padding: 0; size: A4 landscape; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; color: #333; }
        .portada { width: 297mm; height: 210mm; position: relative; overflow: hidden; }
        .portada-izq { position: absolute; top: 0; left: 0; width: 42%; height: 100%; background-color: #000000; color: #ffffff; padding: 40px 30px; }
        .portada-der { position: absolute; top: 0; right: 0; width: 58%; height: 100%; background-color: #1a1a1a; }
        .oferta { font-size: 14px; letter-spacing: 1.5px; margin-bottom: 80px; }
        .titulo { font-size: 34px; font-weight: bold; line-height: 1.15; margin: 0 0 35px 0; text-transform: uppercase; }
        .subtitulo { font-size: 28px; font-weight: bold; margin: 0 0 15px 0; }
        .tipo { font-size: 22px; font-weight: 300; margin: 0; }
        .portada-logo { position: absolute; top: 20px; right: 20px; width: 160px; height: 160px; z-index: 10; }
        .portada-logo svg { width: 160px; height: 160px; }
        .portada-fondo { position: absolute; bottom: 300px; right: 0; width: 100%; height: calc(100% - 180px); object-fit: cover; }
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
        $html .= '<div class="portada">';
        $html .= '<div class="portada-izq">';
        $html .= '<div class="oferta">Oferta ' . $valores['PROYECTO_CODIGO'] . '</div>';
        $html .= '<h1 class="titulo">' . mb_strtoupper($valores['PROYECTO_NOMBRE'], 'UTF-8') . '</h1>';
        $html .= '<div class="subtitulo">PPA</div>';
        $html .= '<div class="tipo">Energía Solar Fotovoltaica</div>';
        $html .= '</div>';
        $html .= '<div class="portada-der">';
        $html .= '<div class="portada-logo">' . $logo . '</div>';
        if ($imagenFondo) {
            $html .= '<img class="portada-fondo" src="' . $imagenFondo . '" alt="" />';
        }
        $html .= '</div>';
        $html .= '</div>';

        // Secciones de la plantilla
        foreach ($secciones as $index => $seccion) {
            $numero = $index + 1;
            $variablesSeccion = $this->plantillaVariableDao->getBySeccionId($seccion['id']);
            $valoresSeccion = $this->construirValoresVariables($variablesSeccion, $valores);
            $valoresCompletos = array_merge($valores, $valoresSeccion);

            $titulo = $seccion['titulo'] ?? $seccion['codigo'];
            if (mb_strtoupper($titulo, 'UTF-8') === 'CONTENIDO') {
                continue;
            }

            $contenido = $seccion['contenido'] ?? '';
            $contenido = $this->reemplazarVariables($contenido, $valoresCompletos);

            $html .= '<div class="contenido-pagina" style="page-break-after: always;">';
            $html .= '<h2>0' . $numero . ' ' . $titulo . '</h2>';
            $html .= '<div class="contenido">' . $contenido . '</div>';
            $html .= '<div class="pie">' . $valores['EMPRESA_NOMBRE'] . ' · ' . $valores['EMPRESA_SITIO_WEB'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</body></html>';
        return $html;
    }

    private function renderContenidoNegro($logo, $variables, $valores) {
        $html = '<div style="width: 297mm; height: 210mm; position: relative; overflow: hidden; background-color: #000000; color: #ffffff; padding: 40px 50px; box-sizing: border-box; page-break-after: always;">';
        $html .= '<div style="position: absolute; top: 20px; right: 20px; width: 160px; height: 160px;">' . $logo . '</div>';
        $html .= '<h1 style="font-size: 42px; font-weight: bold; margin: 0 0 50px 0;">Contenido</h1>';
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">';

        $items = [];
        foreach ($variables as $v) {
            $codigo = $v['codigo'] ?? '';
            $valor = $valores[$codigo] ?? $v['formato'] ?? '';
            if ($valor !== '') {
                $items[] = ['numero' => $codigo, 'texto' => $valor];
            }
        }

        $porFila = 3;
        $chunks = array_chunk($items, $porFila);
        foreach ($chunks as $fila) {
            $html .= '<tr style="height: 80px;">';
            for ($i = 0; $i < $porFila; $i++) {
                $item = $fila[$i] ?? null;
                $html .= '<td width="33%" valign="top" style="padding: 0 30px 40px 0;">';
                if ($item) {
                    $html .= '<div style="font-size: 36px; font-weight: bold; color: #ffffff; margin-bottom: 10px;">0' . $item['numero'] . '</div>';
                    $html .= '<div style="font-size: 16px; font-weight: bold; color: #ffffff; line-height: 1.2;">' . nl2br($item['texto']) . '</div>';
                }
                $html .= '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }

    private function construirValoresVariables($variables, $valoresBase) {
        $mapa = [
            'CODIGODEOFERTA' => 'PROYECTO_CODIGO',
            'EMPRESAOENTIDAD' => 'EMPRESA_NOMBRE',
            'NOMBREDELAPROPUESTA' => 'PROYECTO_NOMBRE',
            'LOGO' => 'EMPRESA_LOGO'
        ];

        $resultado = [];
        foreach ($variables as $v) {
            $codigo = $v['codigo'] ?? '';
            $nombre = $this->normalizarNombre($v['nombre'] ?? '');
            $clave = $mapa[$nombre] ?? $nombre;
            $formato = $v['formato'] ?? '';
            $valor = $formato !== '' ? $formato : ($valoresBase[$clave] ?? '');

            $resultado[$codigo] = $valor;
            $resultado[$nombre] = $valor;
        }
        return $resultado;
    }

    private function normalizarNombre($nombre) {
        $nombre = mb_strtoupper($nombre, 'UTF-8');
        $nombre = preg_replace('/[^A-Z0-9]/', '', $nombre);






        return $nombre;
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
            $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . $ext;
            if ($ext === 'svg') {
                $svg = file_get_contents($ruta);
                $svg = preg_replace('/<\?xml.*?\?>/s', '', $svg);
                $svg = preg_replace('/<!DOCTYPE.*?>/s', '', $svg);
                $svg = preg_replace('/fill="[^"]*"/', 'fill="#ffffff"', $svg);
                $svg = str_replace('fill="#ffffff"none', 'fill="none"', $svg);
                $svg = preg_replace('/(<svg[^>]*>)/', '$1<style>* { fill: #ffffff; }</style>', $svg, 1);
                $svg = preg_replace('/width="[^"]*"/', 'width="300px"', $svg);
                $svg = preg_replace('/height="[^"]*"/', 'height="300px"', $svg);
                $data = trim($svg);
            } else {
                $data = file_get_contents($ruta);
            }
            return '<img src="data:' . $mime . ';base64,' . base64_encode($data) . '" alt="logo" style="width: 300px; height: auto;" />';
        }

        return '';
    }

    private function urlImagenPortada() {
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

        $ruta = '';
        foreach (array_merge($rutasApi, $rutasApp) as $r) {
            if (file_exists($r)) {
                $ruta = $r;
                break;
            }
        }

        if (!$ruta) return '';

        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($docRoot && strpos($ruta, $docRoot) === 0) {
            $rel = str_replace('\\', '/', substr($ruta, strlen($docRoot)));
        } else {
            $rel = '/assets/portada.' . pathinfo($ruta, PATHINFO_EXTENSION);
        }

        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . $rel;
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
