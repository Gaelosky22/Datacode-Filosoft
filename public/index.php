<?php
session_start();
require_once __DIR__ . '/../core/Env.php';
Env::load(__DIR__ . '/../.env');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$controlador = $_GET['r'] ?? 'inicio';
$accion = $_GET['accion'] ?? 'index';

$mapa = [
    'inicio' => 'InicioController',
    'auth' => 'AuthController',
    'alumno' => 'AlumnoController',
    'comite' => 'ComiteController',
    'admin' => 'AdminController',
    'equipo' => 'EquipoController'
];

if (!isset($mapa[$controlador])) {
    http_response_code(404);
    echo "Página no encontrada";
    exit;
}

$claseControlador = $mapa[$controlador];
require_once __DIR__ . "/../controllers/{$claseControlador}.php";

$objeto = new $claseControlador();

if (!method_exists($objeto, $accion)) {
    http_response_code(404);
    echo "Acción no encontrada";
    exit;
}

$objeto->$accion();