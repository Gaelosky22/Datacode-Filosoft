<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/EquipoMiembro.php';

class EquipoController
{
    public function gestionar()
    {
        Auth::requerirRol('docente');

        $eventoId = $_GET['evento_id'] ?? null;
        $eventoModel = new Evento();
        $evento = $eventoModel->obtenerPorId($eventoId);

        if (!$evento || $evento['responsable_id'] !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            require __DIR__ . '/../views/errores/403.php';
            return;
        }

        if (!$evento['es_por_equipos']) {
            echo 'Este evento no está configurado por equipos.';
            return;
        }

        $equipoModel = new Equipo();
        $miembroModel = new EquipoMiembro();
        $equipos = $equipoModel->obtenerPorEvento($eventoId);
        foreach ($equipos as &$equipo) {
            $equipo['ocupados'] = $miembroModel->contarPorEquipo($equipo['id']);
        }
        unset($equipo);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

        require __DIR__ . '/../views/comite/equipos.php';
    }

    public function crear()
    {
        Auth::requerirRol('docente');

        $eventoId = $_POST['evento_id'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $tamanoMax = $_POST['tamano_max'] ?? '';

        $eventoModel = new Evento();
        $evento = $eventoModel->obtenerPorId($eventoId);

        if (!$evento || $evento['responsable_id'] !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            require __DIR__ . '/../views/errores/403.php';
            return;
        }

        if ($nombre === '' || !ctype_digit((string)$tamanoMax) || (int)$tamanoMax < 1) {
            $_SESSION['flash_mensaje'] = 'Nombre y tamaño máximo válido son obligatorios.';
            $_SESSION['flash_tipo'] = 'error';
            header('Location: ?r=equipo&accion=gestionar&evento_id=' . urlencode($eventoId));
            exit;
        }

        (new Equipo())->crear($eventoId, $nombre, (int)$tamanoMax);

        $_SESSION['flash_mensaje'] = 'Equipo "' . $nombre . '" creado.';
        $_SESSION['flash_tipo'] = 'exito';
        header('Location: ?r=equipo&accion=gestionar&evento_id=' . urlencode($eventoId));
        exit;
    }

    public function elegir()
    {
        Auth::requerirRol('alumno');

        $equipoId = $_POST['equipo_id'] ?? null;
        $usuarioId = $_SESSION['usuario']['id'];

        $equipo = (new Equipo())->obtenerPorId($equipoId);

        if (!$equipo) {
            $this->redirigirAAlumno('Ese equipo ya no existe.', 'error');
        }

        $miembroModel = new EquipoMiembro();

        if ($miembroModel->obtenerDelUsuarioEnEvento($equipo['evento_id'], $usuarioId)) {
            $this->redirigirAAlumno('Ya perteneces a un equipo en este evento.', 'error');
        }

        if ($miembroModel->contarPorEquipo($equipoId) >= $equipo['tamano_max']) {
            $this->redirigirAAlumno('Ese equipo ya está lleno, elige otro.', 'error');
        }

        $miembroModel->crear($equipoId, $usuarioId);

        $this->redirigirAAlumno('Te uniste al equipo "' . $equipo['nombre'] . '".', 'exito');
    }

    private function redirigirAAlumno($mensaje, $tipo)
    {
        $_SESSION['flash_mensaje'] = $mensaje;
        $_SESSION['flash_tipo'] = $tipo;
        header('Location: ?r=alumno&accion=index');
        exit;
    }
}