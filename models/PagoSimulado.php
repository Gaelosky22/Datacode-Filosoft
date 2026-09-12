<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class PagoSimulado
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerPorInscripcion($inscripcionId)
    {
        $resultado = $this->db->request(
            '/rest/v1/pagos_simulados?inscripcion_id=eq.' . urlencode($inscripcionId) . '&select=*'
        );
        return $resultado[0] ?? null;
    }

    public function crear($inscripcionId, $monto)
    {
        return $this->db->request('/rest/v1/pagos_simulados', 'POST', [
            'inscripcion_id' => $inscripcionId,
            'monto' => $monto,
            'estado' => 'completado',
        ]);
    }
}