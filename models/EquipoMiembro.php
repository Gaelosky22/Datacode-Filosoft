<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class EquipoMiembro
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function contarPorEquipo($equipoId)
    {
        $resultado = $this->db->request(
            '/rest/v1/equipo_miembros?equipo_id=eq.' . urlencode($equipoId) . '&select=id'
        );
        return count($resultado);
    }

    public function obtenerDelUsuarioEnEvento($eventoId, $usuarioId)
    {
        $resultado = $this->db->request(
            '/rest/v1/equipo_miembros?usuario_id=eq.' . urlencode($usuarioId)
            . '&select=*,equipos!inner(nombre,evento_id)&equipos.evento_id=eq.' . urlencode($eventoId)
        );
        return $resultado[0] ?? null;
    }

    public function crear($equipoId, $usuarioId)
    {
        return $this->db->request('/rest/v1/equipo_miembros', 'POST', [
            'equipo_id' => $equipoId,
            'usuario_id' => $usuarioId,
        ]);
    }
}