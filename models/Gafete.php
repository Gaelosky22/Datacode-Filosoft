<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Gafete
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerPorInscripcion($inscripcionId)
    {
        $resultado = $this->db->request(
            '/rest/v1/gafetes?inscripcion_id=eq.' . urlencode($inscripcionId) . '&select=*'
        );
        return $resultado[0] ?? null;
    }

    public function obtenerCompleto($inscripcionId)
    {
        $resultado = $this->db->request(
            '/rest/v1/gafetes?inscripcion_id=eq.' . urlencode($inscripcionId)
            . '&select=*,inscripciones(usuarios(nombre),eventos(titulo,fecha_hora_inicio,ubicacion))'
        );
        return $resultado[0] ?? null;
    }

    public function crear($inscripcionId, $folio, $tipo)
    {
        return $this->db->request('/rest/v1/gafetes', 'POST', [
            'inscripcion_id' => $inscripcionId,
            'folio' => $folio,
            'tipo' => $tipo,
        ]);
    }

    // El folio dobla como "número de participante": prefijo del evento + secuencial
    public function generarFolio($eventoId, $numeroParticipante)
    {
        $prefijo = strtoupper(substr(str_replace('-', '', $eventoId), 0, 4));
        return $prefijo . '-' . str_pad($numeroParticipante, 3, '0', STR_PAD_LEFT);
    }
}