<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Sede
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function obtenerTodas()
    {
        return $this->db->request('/rest/v1/sedes?select=id,nombre&order=nombre.asc');
    }
}