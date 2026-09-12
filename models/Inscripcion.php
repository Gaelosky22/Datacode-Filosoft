<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Inscripcion
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function delUsuario($usuarioId)
    {
        return $this->db->request(
            '/rest/v1/inscripciones?usuario_id=eq.' . urlencode($usuarioId)
            . '&estado=eq.confirmada&select=*,eventos(id,titulo,fecha_hora_inicio)'
        );
    }

    public function crear($eventoId, $usuarioId)
    {
        return $this->db->request('/rest/v1/inscripciones', 'POST', [
            'evento_id' => $eventoId,
            'usuario_id' => $usuarioId,
        ]);
    }

    public function contarPorEvento($eventoId)
{
    $resultado = $this->db->request(
        '/rest/v1/inscripciones?evento_id=eq.' . urlencode($eventoId) . '&select=id'
    );
    return count($resultado);
}
}