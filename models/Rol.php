<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Rol
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerIdPorNombre($nombre)
    {
        $resultado = $this->db->request('/rest/v1/roles?nombre=eq.' . urlencode($nombre) . '&select=id');
        return $resultado[0]['id'] ?? null;
    }
}