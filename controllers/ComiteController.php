<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Sede.php';
require_once __DIR__ . '/../models/EventoSede.php';
require_once __DIR__ . '/../models/Voto.php';
require_once __DIR__ . '/../models/Comentario.php';
class ComiteController
{
    public function index()
    {
        Auth::requerirAlgunRol(['comite', 'docente', 'coordinacion', 'administrador']);

        $roles = $_SESSION['usuario']['roles'] ?? [];
        $esComite = in_array('comite', $roles);
        $esDocente = in_array('docente', $roles);
        $esCoordinacion = in_array('coordinacion', $roles);
        $esAdministrador = in_array('administrador', $roles);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

        require __DIR__ . '/../views/comite/portal.php';
    }

    public function propuestas()
{
    Auth::requerirAlgunRol(['comite', 'administrador']);

    $eventoModel = new Evento();
    $eventos = $eventoModel->obtenerPropuestasParaComite($_SESSION['usuario']['sede_id']);

    require __DIR__ . '/../views/comite/propuestas.php';
}

public function propuesta()
{
    Auth::requerirAlgunRol(['comite', 'administrador']);

    $eventoId = $_GET['id'] ?? null;
    $eventoModel = new Evento();
    $evento = $eventoModel->obtenerPorId($eventoId);

    if (!$evento) {
        http_response_code(404);
        echo 'Propuesta no encontrada';
        return;
    }

    $eventoSedeModel = new EventoSede();
    $miSedeId = $_SESSION['usuario']['sede_id'];

    if (!$eventoSedeModel->sedeTienePermiso($eventoId, $miSedeId)) {
        http_response_code(403);
        require __DIR__ . '/../views/errores/403.php';
        return;
    }

    $votoModel = new Voto();
    $conteo = $votoModel->contarPorEvento($eventoId);
    $yaVoto = $votoModel->yaVoto($eventoId, $_SESSION['usuario']['id']);
    $votacionAbierta = strtotime($evento['fecha_cierre_votacion']) > time();

    $comentarioModel = new Comentario();
    $comentarios = $comentarioModel->obtenerPorEvento($eventoId);

    $error = $_SESSION['flash_mensaje'] ?? null;
    $errorTipo = $_SESSION['flash_tipo'] ?? null;
    unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

    require __DIR__ . '/../views/comite/propuesta_detalle.php';
}

public function votar()
{
    Auth::requerirAlgunRol(['comite', 'administrador']);

    $eventoId = $_POST['evento_id'] ?? null;
    $sentido = $_POST['sentido'] ?? null;
    $usuarioId = $_SESSION['usuario']['id'];

    if (!in_array($sentido, ['a_favor', 'en_contra'])) {
        $this->redirigirAPropuesta($eventoId, 'Voto inválido.', 'error');
    }

    $eventoModel = new Evento();
    $evento = $eventoModel->obtenerPorId($eventoId);

    if (!$evento || $evento['estado'] !== 'propuesta') {
        $this->redirigirAPropuesta($eventoId, 'Esta propuesta ya no admite votos.', 'error');
    }

    if (strtotime($evento['fecha_cierre_votacion']) <= time()) {
        $this->redirigirAPropuesta($eventoId, 'La votación para esta propuesta ya cerró.', 'error');
    }

    $eventoSedeModel = new EventoSede();
    if (!$eventoSedeModel->sedeTienePermiso($eventoId, $_SESSION['usuario']['sede_id'])) {
        $this->redirigirAPropuesta($eventoId, 'Tu sede no tiene permiso para votar esta propuesta.', 'error');
    }

    $votoModel = new Voto();
    if ($votoModel->yaVoto($eventoId, $usuarioId)) {
        $this->redirigirAPropuesta($eventoId, 'Ya emitiste tu voto en esta propuesta.', 'error');
    }

    $votoModel->crear($eventoId, $usuarioId, $sentido);

    $this->redirigirAPropuesta($eventoId, 'Tu voto quedó registrado.', 'exito');
}

public function comentar()
{
    Auth::requerirAlgunRol(['comite', 'administrador']);

    $eventoId = $_POST['evento_id'] ?? null;
    $texto = trim($_POST['texto'] ?? '');

    if ($texto === '') {
        $this->redirigirAPropuesta($eventoId, 'Escribe un comentario antes de enviarlo.', 'error');
    }

    $eventoSedeModel = new EventoSede();
    if (!$eventoSedeModel->sedeTienePermiso($eventoId, $_SESSION['usuario']['sede_id'])) {
        $this->redirigirAPropuesta($eventoId, 'Tu sede no tiene permiso para comentar esta propuesta.', 'error');
    }

    $comentarioModel = new Comentario();
    $comentarioModel->crear($eventoId, $_SESSION['usuario']['id'], $texto);

    $this->redirigirAPropuesta($eventoId, 'Comentario publicado.', 'exito');
}

private function redirigirAPropuesta($eventoId, $mensaje, $tipo)
{
    $_SESSION['flash_mensaje'] = $mensaje;
    $_SESSION['flash_tipo'] = $tipo;
    header('Location: ?r=comite&accion=propuesta&id=' . urlencode($eventoId));
    exit;
}

    public function proponer()
    {
        Auth::requerirRol('docente');

        $sedeModel = new Sede();
        $sedes = $sedeModel->obtenerTodas();
        $error = null;

        require __DIR__ . '/../views/comite/proponer.php';
    }

public function guardarPropuesta()
{
    Auth::requerirRol('docente');

    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $sedeId = $_POST['sede_id'] ?? '';
    $fechaInicio = $_POST['fecha_hora_inicio'] ?? '';
    $fechaFin = $_POST['fecha_hora_fin'] ?? '';
    $cupo = $_POST['cupo'] ?? '';
    $fechaCierreVotacion = $_POST['fecha_cierre_votacion'] ?? '';
    $costoPresupuestado = $_POST['costo_presupuestado'] ?? '0';
    $tieneCuota = isset($_POST['tiene_cuota']);
    $cuota = $_POST['cuota'] ?? '0';
    $esPorEquipos = isset($_POST['es_por_equipos']);
    $tamanoEquipoMax = $_POST['tamano_equipo_max'] ?? '';

    $errores = [];

    if ($titulo === '' || $tipo === '' || $sedeId === '') {
        $errores[] = 'Completa título, tipo y sede.';
    }

    if (!ctype_digit((string)$cupo) || (int)$cupo < 1) {
        $errores[] = 'El cupo debe ser un número entero mayor a 0.';
    }

    if (!is_numeric($costoPresupuestado) || (float)$costoPresupuestado < 0) {
        $errores[] = 'El costo presupuestado no puede ser negativo.';
    }

    if ($tieneCuota && (!is_numeric($cuota) || (float)$cuota < 0)) {
        $errores[] = 'La cuota no puede ser negativa.';
    }

    if ($esPorEquipos && $tamanoEquipoMax !== '' && (!ctype_digit((string)$tamanoEquipoMax) || (int)$tamanoEquipoMax < 1)) {
        $errores[] = 'El tamaño de equipo debe ser un número entero mayor a 0.';
    }

    $ahora = time();
    $tsInicio = strtotime($fechaInicio);
    $tsFin = strtotime($fechaFin);
    $tsCierreVotacion = strtotime($fechaCierreVotacion);

    if (!$fechaInicio || !$fechaFin || !$fechaCierreVotacion) {
        $errores[] = 'Completa las tres fechas: inicio, fin y cierre de votación.';
    } else {
        if ($tsInicio < $ahora) {
            $errores[] = 'La fecha de inicio no puede ser en el pasado.';
        }
        if ($tsFin <= $tsInicio) {
            $errores[] = 'La fecha de fin debe ser posterior a la fecha de inicio.';
        }
        if ($tsCierreVotacion < $ahora) {
            $errores[] = 'La fecha de cierre de votación no puede ser en el pasado.';
        }
        if ($tsCierreVotacion > $tsInicio) {
            $errores[] = 'La votación debe cerrar antes de que inicie el evento.';
        }
    }

    if (!empty($errores)) {
        $error = implode(' ', $errores);
        $sedeModel = new Sede();
        $sedes = $sedeModel->obtenerTodas();
        require __DIR__ . '/../views/comite/proponer.php';
        return;
    }

    $cupo = (int)$cupo;

    $eventoModel = new Evento();
    $nuevoEvento = $eventoModel->crear([
        'titulo' => $titulo,
        'tipo' => $tipo,
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'sede_id' => $sedeId,
        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
        'espacio' => trim($_POST['espacio'] ?? ''),
        'responsable_id' => $_SESSION['usuario']['id'],
        'fecha_hora_inicio' => $fechaInicio,
        'fecha_hora_fin' => $fechaFin,
        'cupo' => $cupo,
        'requisitos' => trim($_POST['requisitos'] ?? ''),
        'costo_presupuestado' => (float)$costoPresupuestado,
        'tiene_cuota' => $tieneCuota,
        'cuota' => $tieneCuota ? (float)$cuota : 0,
        'es_por_equipos' => $esPorEquipos,
        'modalidad_equipos' => $esPorEquipos ? ($_POST['modalidad_equipos'] ?? null) : null,
        'tamano_equipo_max' => $esPorEquipos ? (int)$tamanoEquipoMax : null,
        'fecha_cierre_votacion' => $fechaCierreVotacion,
        'estado' => 'propuesta',
    ]);

    $eventoId = $nuevoEvento[0]['id'] ?? null;

    if ($eventoId) {
        $sedesPermitidas = $_POST['sedes_permitidas'] ?? [];
        $sedesPermitidas[] = $sedeId;

        $eventoSedeModel = new EventoSede();
        $eventoSedeModel->asignar($eventoId, $sedesPermitidas);
    }

    $_SESSION['flash_mensaje'] = 'Tu propuesta "' . $titulo . '" fue enviada al comité para votación.';
    $_SESSION['flash_tipo'] = 'exito';
    header('Location: ?r=comite&accion=index');
    exit;
}
}