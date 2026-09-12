<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Comentario
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerPorEvento($eventoId)
    {
        return $this->db->request(
            '/rest/v1/comentarios?evento_id=eq.' . urlencode($eventoId)
            . '&select=*,usuarios(nombre)&order=fecha.asc'
        );
    }

    public function crear($eventoId, $usuarioId, $texto)
    {
        return $this->db->request('/rest/v1/comentarios', 'POST', [
            'evento_id' => $eventoId,
            'usuario_id' => $usuarioId,
            'texto' => $texto,
        ]);
    }
}