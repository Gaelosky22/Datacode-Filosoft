<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class UsuarioRol
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function asignar($usuarioId, $rolId)
    {
        return $this->db->request('/rest/v1/usuario_roles', 'POST', [
            'usuario_id' => $usuarioId,
            'rol_id' => $rolId,
        ]);
    }

    public function quitar($usuarioId, $rolId)
    {
        return $this->db->request(
            '/rest/v1/usuario_roles?usuario_id=eq.' . urlencode($usuarioId) . '&rol_id=eq.' . urlencode($rolId),
            'DELETE'
        );
    }
}