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

public function crear($datos)
{
    return $this->db->request('/rest/v1/usuarios', 'POST', $datos);
}

public function obtenerTodos($sedeId = null, $busqueda = null)
{
    $filtros = '';

    if ($sedeId) {
        $filtros .= '&sede_id=eq.' . urlencode($sedeId);
    }

    if ($busqueda) {
        $texto = urlencode('%' . $busqueda . '%');
        $filtros .= '&or=(matricula.ilike.' . $texto . ',nombre.ilike.' . $texto . ')';
    }

    return $this->db->request(
        '/rest/v1/usuarios?select=*,sedes(nombre),usuario_roles(id,roles(id,nombre))' . $filtros . '&order=nombre.asc'
    );
}

}