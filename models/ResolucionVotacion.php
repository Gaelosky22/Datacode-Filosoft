<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class ResolucionVotacion
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function crear($datos)
    {
        return $this->db->request('/rest/v1/resoluciones_votacion', 'POST', $datos);
    }

    public function obtenerPorEvento($eventoId)
    {
        $resultado = $this->db->request(
            '/rest/v1/resoluciones_votacion?evento_id=eq.' . urlencode($eventoId)
            . '&select=*&order=creado_en.desc'
        );
        return $resultado[0] ?? null;
    }
}