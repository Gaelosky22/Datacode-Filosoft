<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Equipo
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function crear($eventoId, $nombre, $tamanoMax)
    {
        return $this->db->request('/rest/v1/equipos', 'POST', [
            'evento_id' => $eventoId,
            'nombre' => $nombre,
            'tamano_max' => $tamanoMax,
        ]);
    }

    public function obtenerPorEvento($eventoId)
    {
        return $this->db->request(
            '/rest/v1/equipos?evento_id=eq.' . urlencode($eventoId) . '&select=*&order=nombre.asc'
        );
    }

    public function obtenerPorId($id)
    {
        $resultado = $this->db->request('/rest/v1/equipos?id=eq.' . urlencode($id) . '&select=*');
        return $resultado[0] ?? null;
    }
}