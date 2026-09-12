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

    public function obtenerTodos()
    {
        return $this->db->request('/rest/v1/roles?select=id,nombre&order=nombre.asc');
    }

    public function contarMiembrosComite()
    {
        $resultado = $this->db->request(
            '/rest/v1/usuario_roles?select=usuario_id,roles!inner(nombre)&roles.nombre=eq.comite'
        );
        return count($resultado);
    }
}