<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class EventoSede
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function asignar($eventoId, array $sedeIds)
    {
        $filas = [];
        foreach (array_unique($sedeIds) as $sedeId) {
            $filas[] = ['evento_id' => $eventoId, 'sede_id' => $sedeId];
        }
        if (!empty($filas)) {
            $this->db->request('/rest/v1/evento_sedes_permitidas', 'POST', $filas);
        }
    }

    public function sedeTienePermiso($eventoId, $sedeId)
{
    $resultado = $this->db->request(
        '/rest/v1/evento_sedes_permitidas?evento_id=eq.' . urlencode($eventoId)
        . '&sede_id=eq.' . urlencode($sedeId) . '&select=id'
    );
    return !empty($resultado);
}
}