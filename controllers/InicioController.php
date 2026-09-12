<?php
require_once __DIR__ . '/../models/Evento.php';

class InicioController
{
    
    public function index()
    {
        $modelo = new Evento();
        $eventos = $modelo->obtenerPublicados();
        require __DIR__ . '/../views/inicio.php';
    }

    

    public function detalle()
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo "Falta el ID del evento";
        return;
    }

    $modelo = new Evento();
    $evento = $modelo->obtenerPorId($id);

    if (!$evento) {
        http_response_code(404);
        echo "Evento no encontrado";
        return;
    }

    require __DIR__ . '/../views/evento_detalle.php';
}
    public function detalleModal()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo 'Falta el ID del evento';
        return;
    }

    $modelo = new Evento();
    $evento = $modelo->obtenerPorId($id);

    if (!$evento) {
        http_response_code(404);
        echo 'Evento no encontrado';
        return;
    }

    require __DIR__ . '/../views/partials/evento_detalle_modal.php';
}

public function historial()
{
    $modelo = new Evento();
    $eventos = $modelo->obtenerPasados();
    require __DIR__ . '/../views/historial.php';
}
}