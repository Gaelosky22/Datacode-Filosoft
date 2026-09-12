<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerPorMatricula($matricula)
{
    $resultado = $this->db->request(
        '/rest/v1/usuarios?matricula=eq.' . urlencode($matricula)
        . '&select=*,sedes(nombre),usuario_roles(roles(nombre))'
    );

    return $resultado[0] ?? null;
}
}