<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Gafete.php';
require_once __DIR__ . '/../models/PagoSimulado.php';

class AlumnoController
{
    public function index()
    {
        Auth::requerirLogin();

        $sedeId = $_SESSION['usuario']['sede_id'];

        $eventoModel = new Evento();
        $eventos = $eventoModel->obtenerDisponiblesParaAlumno($sedeId);

        $inscripcionModel = new Inscripcion();
        $misInscripciones = $inscripcionModel->delUsuario($_SESSION['usuario']['id']);

        $inscripcionesPorEvento = [];
        foreach ($misInscripciones as $insc) {
            $inscripcionesPorEvento[$insc['evento_id']] = $insc;
        }

        $gafeteModel = new Gafete();
        $pagoModel = new PagoSimulado();

        $estadoInscripcion = [];
        foreach ($inscripcionesPorEvento as $eventoId => $insc) {
            $estadoInscripcion[$eventoId] = [
                'inscripcion_id' => $insc['id'],
                'gafete' => $gafeteModel->obtenerPorInscripcion($insc['id']),
                'pago' => $pagoModel->obtenerPorInscripcion($insc['id']),
            ];
        }

        $esAlumno = in_array('alumno', $_SESSION['usuario']['roles'] ?? []);

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
            $this->redirigirConError('Ese evento ya no existe.');
        }

        $inscripcionModel = new Inscripcion();
        $existentes = $inscripcionModel->delUsuario($usuarioId);
        $fechaNueva = date('Y-m-d', strtotime($evento['fecha_hora_inicio']));

        foreach ($existentes as $insc) {
            if ($insc['evento_id'] === $eventoId) {
                $this->redirigirConError('Ya estás inscrito a este evento.');
            }
            $fechaExistente = date('Y-m-d', strtotime($insc['eventos']['fecha_hora_inicio']));
            if ($fechaExistente === $fechaNueva) {
                $this->redirigirConError('Ya tienes "' . $insc['eventos']['titulo'] . '" inscrito ese mismo día.');
            }
        }

        $nuevaInscripcion = $inscripcionModel->crear($eventoId, $usuarioId);
        $inscripcionId = $nuevaInscripcion[0]['id'] ?? null;

        if (!$inscripcionId) {
            $this->redirigirConError('No se pudo completar tu inscripción. Intenta de nuevo.');
        }

        if (!$evento['tiene_cuota']) {
            $gafeteModel = new Gafete();
            $numeroParticipante = $inscripcionModel->contarPorEvento($eventoId);
            $folio = $gafeteModel->generarFolio($eventoId, $numeroParticipante);
            $gafeteModel->crear($inscripcionId, $folio, 'alumno');

            $this->redirigirConExito('Te inscribiste a "' . $evento['titulo'] . '". Tu gafete ya está listo.');
        }

        $this->redirigirConExito('Te inscribiste a "' . $evento['titulo'] . '". Simula tu pago para generar tu gafete.');
    }

    public function simularPago()
    {
        Auth::requerirRol('alumno');

        $inscripcionId = $_POST['inscripcion_id'] ?? null;
        $eventoId = $_POST['evento_id'] ?? null;

        if (!$inscripcionId || !$eventoId) {
            $this->redirigirConError('Faltan datos para procesar el pago.');
        }

        $pagoModel = new PagoSimulado();

        if ($pagoModel->obtenerPorInscripcion($inscripcionId)) {
            $this->redirigirConError('Ya habías simulado el pago de este evento.');
        }

        $eventoModel = new Evento();
        $evento = $eventoModel->obtenerPorId($eventoId);

        $pagoModel->crear($inscripcionId, $evento['cuota']);

        $gafeteModel = new Gafete();
        $inscripcionModel = new Inscripcion();
        $numeroParticipante = $inscripcionModel->contarPorEvento($eventoId);
        $folio = $gafeteModel->generarFolio($eventoId, $numeroParticipante);
        $gafeteModel->crear($inscripcionId, $folio, 'alumno');

        $this->redirigirConExito('Pago realizado — simulación. No se efectuó ningún cobro real. Tu gafete ya está listo.');
    }

    public function gafete()
    {
        Auth::requerirLogin();

        $inscripcionId = $_GET['id'] ?? null;
        $gafeteModel = new Gafete();
        $gafete = $gafeteModel->obtenerCompleto($inscripcionId);

        if (!$gafete) {
            http_response_code(404);
            echo 'Gafete no encontrado';
            return;
        }

        require __DIR__ . '/../views/alumno/gafete.php';
    }

    private function redirigirConError($mensaje)
    {
        $_SESSION['flash_mensaje'] = $mensaje;
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ?r=alumno&accion=index');
        exit;
    }

    private function redirigirConExito($mensaje)
    {
        $_SESSION['flash_mensaje'] = $mensaje;
        $_SESSION['flash_tipo'] = 'exito';
        header('Location: ?r=alumno&accion=index');
        exit;
    }
}