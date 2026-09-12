<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Gafete.php';
require_once __DIR__ . '/../models/PagoSimulado.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/EquipoMiembro.php';

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
        $equipoModel = new Equipo();
        $miembroModel = new EquipoMiembro();

        $estadoInscripcion = [];
        foreach ($inscripcionesPorEvento as $eventoId => $insc) {
            $estadoInscripcion[$eventoId] = [
                'inscripcion_id' => $insc['id'],
                'gafete' => $gafeteModel->obtenerPorInscripcion($insc['id']),
                'pago' => $pagoModel->obtenerPorInscripcion($insc['id']),
                'equipo' => $miembroModel->obtenerDelUsuarioEnEvento($eventoId, $_SESSION['usuario']['id']),
            ];
        }

        // Para eventos por equipos en modalidad "libre" donde el alumno ya está
        // inscrito pero aún no tiene equipo, calculamos cuáles tienen lugar.
        $equiposLibresPorEvento = [];
        foreach ($eventos as $evento) {
            $yaTieneEquipo = !empty($estadoInscripcion[$evento['id']]['equipo']);
            if ($evento['es_por_equipos'] && $evento['modalidad_equipos'] === 'libre' && !$yaTieneEquipo) {
                $conEspacio = [];
                foreach ($equipoModel->obtenerPorEvento($evento['id']) as $eq) {
                    if ($miembroModel->contarPorEquipo($eq['id']) < $eq['tamano_max']) {
                        $conEspacio[] = $eq;
                    }
                }
                $equiposLibresPorEvento[$evento['id']] = $conEspacio;
            }
        }

        $esAlumno = in_array('alumno', $_SESSION['usuario']['roles'] ?? []);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $mensajeTipo = $_SESSION['flash_tipo'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

        require __DIR__ . '/../views/alumno/portal.php';
    }

    public function misEventosModal()
    {
        Auth::requerirRol('alumno');

        $vista = $_GET['vista'] ?? 'inscrito';
        $usuarioId = $_SESSION['usuario']['id'];

        $inscripcionModel = new Inscripcion();
        $misInscripciones = $inscripcionModel->delUsuario($usuarioId);

        $gafeteModel = new Gafete();
        $estadoInscripcion = [];
        foreach ($misInscripciones as $insc) {
            $estadoInscripcion[$insc['evento_id']] = [
                'inscripcion_id' => $insc['id'],
                'gafete' => $gafeteModel->obtenerPorInscripcion($insc['id']),
            ];
        }

        require __DIR__ . '/../views/partials/mis_eventos_modal.php';
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

        if ((int)$evento['cupo'] <= 0) {
            $this->redirigirConError('Lo sentimos, este evento ya no tiene lugares disponibles.');
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

        if ($evento['es_por_equipos'] && $evento['modalidad_equipos'] === 'aleatoria') {
            $equipoModel = new Equipo();
            $miembroModel = new EquipoMiembro();

            $conEspacio = [];
            foreach ($equipoModel->obtenerPorEvento($eventoId) as $eq) {
                if ($miembroModel->contarPorEquipo($eq['id']) < $eq['tamano_max']) {
                    $conEspacio[] = $eq;
                }
            }

            if (!empty($conEspacio)) {
                $elegido = $conEspacio[array_rand($conEspacio)];
                $miembroModel->crear($elegido['id'], $usuarioId);
            }
        }

        if (!$evento['tiene_cuota']) {
            $gafeteModel = new Gafete();
            $numeroParticipante = $inscripcionModel->contarPorEvento($eventoId);
            $folio = $gafeteModel->generarFolio($eventoId, $numeroParticipante);
            $gafeteModel->crear($inscripcionId, $folio, 'alumno');

            $eventoModel->reducirCupo($eventoId);

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

        $eventoModel->reducirCupo($eventoId);

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