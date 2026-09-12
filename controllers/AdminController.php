<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Sede.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/UsuarioRol.php';

require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Voto.php';
require_once __DIR__ . '/../models/Comentario.php';

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

public function propuestas()
{
    Auth::requerirRol('administrador');

    $eventoModel = new Evento();
    $eventos = $eventoModel->obtenerTodasPropuestas();

    require __DIR__ . '/../views/admin/propuestas.php';
}


public function guardarEdicion()
{
    Auth::requerirRol('administrador');

    $eventoId = $_POST['evento_id'] ?? null;
    $eventoModel = new Evento();
    $evento = $eventoModel->obtenerPorId($eventoId);

    if (!$evento || $evento['estado'] !== 'propuesta') {
        $this->redirigirAResolver($eventoId, 'Solo se pueden editar propuestas que sigan en votación.', 'error');
    }

    $cupo = $_POST['cupo'] ?? '';
    $costoPresupuestado = $_POST['costo_presupuestado'] ?? '0';
    $tieneCuota = isset($_POST['tiene_cuota']);
    $cuota = $_POST['cuota'] ?? '0';

    if (!ctype_digit((string)$cupo) || (int)$cupo < 1) {
        $this->redirigirAResolver($eventoId, 'El cupo debe ser un número entero mayor a 0.', 'error');
    }
    if (!is_numeric($costoPresupuestado) || (float)$costoPresupuestado < 0) {
        $this->redirigirAResolver($eventoId, 'El costo presupuestado no puede ser negativo.', 'error');
    }
    if ($tieneCuota && (!is_numeric($cuota) || (float)$cuota < 0)) {
        $this->redirigirAResolver($eventoId, 'La cuota no puede ser negativa.', 'error');
    }

    $eventoModel->actualizar($eventoId, [
        'titulo' => trim($_POST['titulo'] ?? $evento['titulo']),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
        'espacio' => trim($_POST['espacio'] ?? ''),
        'requisitos' => trim($_POST['requisitos'] ?? ''),
        'cupo' => (int)$cupo,
        'costo_presupuestado' => (float)$costoPresupuestado,
        'tiene_cuota' => $tieneCuota,
        'cuota' => $tieneCuota ? (float)$cuota : 0,
    ]);

    $this->redirigirAResolver($eventoId, 'Cambios guardados.', 'exito');
}

public function resolver()
{
    Auth::requerirRol('administrador');

    $eventoId = $_GET['id'] ?? null;
    $eventoModel = new Evento();
    $evento = $eventoModel->obtenerPorId($eventoId);

    if (!$evento) {
        http_response_code(404);
        echo 'Propuesta no encontrada';
        return;
    }

    $calculo = $this->calcularResolucion($eventoId, $evento);
    $votacionCerrada = strtotime($evento['fecha_cierre_votacion']) <= time();

    $comentarioModel = new Comentario();
    $comentarios = $comentarioModel->obtenerPorEvento($eventoId);

    $mensaje = $_SESSION['flash_mensaje'] ?? null;
    $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
    unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

    extract($calculo); // $conteo, $quorumRequerido, $quorumAlcanzado, $ambosColectivos, $resultadoPrevisto, $justificacionPrevista

    require __DIR__ . '/../views/admin/resolver.php';
}

public function registrarResolucion()
{
    Auth::requerirRol('administrador');

    $eventoId = $_POST['evento_id'] ?? null;
    $eventoModel = new Evento();
    $evento = $eventoModel->obtenerPorId($eventoId);

    if (!$evento || $evento['estado'] !== 'propuesta') {
        $this->redirigirAResolver($eventoId, 'Esta propuesta ya fue resuelta.', 'error');
    }

    if (strtotime($evento['fecha_cierre_votacion']) > time()) {
        $this->redirigirAResolver($eventoId, 'La votación sigue abierta, no se puede resolver todavía.', 'error');
    }

    $calculo = $this->calcularResolucion($eventoId, $evento);

    $resolucionModel = new ResolucionVotacion();
    $resolucionModel->crear([
        'evento_id' => $eventoId,
        'votos_a_favor' => $calculo['conteo']['a_favor'],
        'votos_en_contra' => $calculo['conteo']['en_contra'],
        'quorum_requerido' => $calculo['quorumRequerido'],
        'quorum_alcanzado' => $calculo['quorumAlcanzado'],
        'resultado' => $calculo['resultadoPrevisto'],
        'justificacion' => $calculo['justificacionPrevista'],
        'registrada_por' => $_SESSION['usuario']['id'],
    ]);

    $eventoModel->actualizar($eventoId, $calculo['resultadoPrevisto'] === 'aprobada'
        ? ['estado' => 'publicado', 'bloqueado' => true]
        : ['estado' => 'rechazado']
    );

    $_SESSION['flash_mensaje'] = 'Propuesta "' . $evento['titulo'] . '" ' . $calculo['resultadoPrevisto'] . '. ' . $calculo['justificacionPrevista'];
    $_SESSION['flash_tipo'] = $calculo['resultadoPrevisto'] === 'aprobada' ? 'exito' : 'error';
    header('Location: ?r=admin&accion=propuestas');
    exit;
}

private function calcularResolucion($eventoId, $evento)
{
    $votoModel = new Voto();
    $conteo = $votoModel->contarPorEvento($eventoId);
    $colectivos = $votoModel->obtenerColectivosQueVotaron($eventoId);

    $rolModel = new Rol();
    $totalComite = $rolModel->contarMiembrosComite();
    $quorumRequerido = (int)ceil($totalComite / 2);
    $totalVotos = $conteo['a_favor'] + $conteo['en_contra'];
    $quorumAlcanzado = $totalComite > 0 && $totalVotos >= $quorumRequerido;
    $ambosColectivos = $colectivos['alumno'] && $colectivos['docente'];

    if (!$quorumAlcanzado) {
        $resultado = 'rechazada';
        $justificacion = "Sin quórum: se necesitaban al menos {$quorumRequerido} votos ({$totalVotos} emitidos de {$totalComite} miembros del comité).";
    } elseif (!$ambosColectivos) {
        $resultado = 'rechazada';
        $justificacion = 'No participaron ambos colectivos (alumnos y docentes) en la votación.';
    } elseif ($conteo['a_favor'] > $conteo['en_contra']) {
        $resultado = 'aprobada';
        $justificacion = "Mayoría a favor ({$conteo['a_favor']} a favor, {$conteo['en_contra']} en contra).";
    } elseif ($conteo['a_favor'] === $conteo['en_contra']) {
        $resultado = 'rechazada';
        $justificacion = "Empate ({$conteo['a_favor']}-{$conteo['en_contra']}): sin voto de desempate, la propuesta se rechaza.";
    } else {
        $resultado = 'rechazada';
        $justificacion = "Mayoría en contra ({$conteo['en_contra']} en contra, {$conteo['a_favor']} a favor).";
    }

    return [
        'conteo' => $conteo,
        'quorumRequerido' => $quorumRequerido,
        'quorumAlcanzado' => $quorumAlcanzado,
        'ambosColectivos' => $ambosColectivos,
        'resultadoPrevisto' => $resultado,
        'justificacionPrevista' => $justificacion,
    ];
}

private function redirigirAResolver($eventoId, $mensaje, $tipo)
{
    $_SESSION['flash_mensaje'] = $mensaje;
    $_SESSION['flash_tipo'] = $tipo;
    header('Location: ?r=admin&accion=resolver&id=' . urlencode($eventoId));
    exit;
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