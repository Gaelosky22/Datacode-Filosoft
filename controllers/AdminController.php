<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Sede.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/UsuarioRol.php';

class AdminController
{
   public function comite()
{
    Auth::requerirRol('administrador');

    $sedeModel = new Sede();
    $sedes = $sedeModel->obtenerTodas();

    $sedeFiltro = $_GET['sede_id'] ?? '';
    $busqueda = trim($_GET['busqueda'] ?? '');

    $usuarioModel = new Usuario();
    $usuarios = $usuarioModel->obtenerTodos($sedeFiltro ?: null, $busqueda ?: null);

    $mensaje = $_SESSION['flash_mensaje'] ?? null;
    $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
    unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

    require __DIR__ . '/../views/admin/comite.php';
}

    public function toggleComite()
{
    Auth::requerirRol('administrador');

    $usuarioId = $_POST['usuario_id'] ?? null;
    $accionToggle = $_POST['accion_toggle'] ?? null;
    $sedeFiltro = $_POST['sede_id'] ?? '';
    $busqueda = $_POST['busqueda'] ?? '';

    $rolModel = new Rol();
    $rolComiteId = $rolModel->obtenerIdPorNombre('comite');

    $usuarioRolModel = new UsuarioRol();

    if ($accionToggle === 'agregar') {
        $usuarioRolModel->asignar($usuarioId, $rolComiteId);
        $_SESSION['flash_mensaje'] = 'Usuario agregado al comité.';
    } else {
        $usuarioRolModel->quitar($usuarioId, $rolComiteId);
        $_SESSION['flash_mensaje'] = 'Usuario removido del comité.';
    }
    $_SESSION['flash_tipo'] = 'exito';

    $destino = '?r=admin&accion=comite';
    $parametros = [];
    if ($sedeFiltro) $parametros[] = 'sede_id=' . urlencode($sedeFiltro);
    if ($busqueda) $parametros[] = 'busqueda=' . urlencode($busqueda);
    if ($parametros) $destino .= '&' . implode('&', $parametros);

    header('Location: ' . $destino);
    exit;
}
}