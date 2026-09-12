<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Evento
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerPublicados()
    {
        return $this->db->request(
            '/rest/v1/eventos?estado=eq.publicado&select=*,sedes(nombre)&order=fecha_hora_inicio.asc'
        );
    }

    public function obtenerPorId($id)
    {
        $resultado = $this->db->request(
            '/rest/v1/eventos?id=eq.' . urlencode($id) . '&select=*,sedes(nombre),responsable:usuarios!eventos_responsable_id_fkey(nombre)'
        );

        return $resultado[0] ?? null;
    }

    public function obtenerPasados()
{
    $ahora = date('c');
    return $this->db->request(
        '/rest/v1/eventos?fecha_hora_fin=lt.' . urlencode($ahora) . '&select=*,sedes(nombre)&order=fecha_hora_fin.desc'
    );
}

public function obtenerDisponiblesParaAlumno($sedeId)
{
    return $this->db->request(
        '/rest/v1/eventos?estado=eq.publicado&sede_id=eq.' . urlencode($sedeId)
        . '&select=*,sedes(nombre)&order=fecha_hora_inicio.asc'
    );
}

}