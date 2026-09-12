<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';

class AlumnoController
{
    public function index()
    {
        Auth::requerirRol('alumno');

        $eventoModel = new Evento();
        $eventos = $eventoModel->obtenerDisponiblesParaAlumno($_SESSION['usuario']['sede_id']);

        $inscripcionModel = new Inscripcion();
        $misInscripciones = $inscripcionModel->delUsuario($_SESSION['usuario']['id']);
        $idsInscritos = array_column($misInscripciones, 'evento_id');

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

        require __DIR__ . '/../views/alumno/portal.php';
    }

    public function inscribir()
    {
        Auth::requerirRol('alumno');

        $eventoId = $_POST['evento_id'] ?? null;
        $usuarioId = $_SESSION['usuario']['id'];

        $eventoModel = new Evento();
        $evento = $eventoModel->obtenerPorId($eventoId);

        if (!$evento) {
            $_SESSION['flash_mensaje'] = 'Ese evento ya no existe.';
            $_SESSION['flash_tipo'] = 'error';
            header('Location: ?r=alumno&accion=index');
            exit;
        }

        $inscripcionModel = new Inscripcion();
        $existentes = $inscripcionModel->delUsuario($usuarioId);
        $fechaNueva = date('Y-m-d', strtotime($evento['fecha_hora_inicio']));

        foreach ($existentes as $insc) {
            if ($insc['evento_id'] === $eventoId) {
                $_SESSION['flash_mensaje'] = 'Ya estás inscrito a este evento.';
                $_SESSION['flash_tipo'] = 'error';
                header('Location: ?r=alumno&accion=index');
                exit;
            }
            $fechaExistente = date('Y-m-d', strtotime($insc['eventos']['fecha_hora_inicio']));
            if ($fechaExistente === $fechaNueva) {
                $_SESSION['flash_mensaje'] = 'Ya tienes "' . $insc['eventos']['titulo'] . '" inscrito ese mismo día.';
                $_SESSION['flash_tipo'] = 'error';
                header('Location: ?r=alumno&accion=index');
                exit;
            }
        }

        $inscripcionModel->crear($eventoId, $usuarioId);

        $_SESSION['flash_mensaje'] = 'Te inscribiste a "' . $evento['titulo'] . '" correctamente.';
        $_SESSION['flash_tipo'] = 'exito';
        header('Location: ?r=alumno&accion=index');
        exit;
    }
}