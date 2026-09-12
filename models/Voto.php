<?php
require_once __DIR__ . '/../core/SupabaseClient.php';

class Voto
{
    private $db;

    public function __construct()
    {
        $this->db = new SupabaseClient();
    }

    public function contarPorEvento($eventoId)
    {
        $votos = $this->db->request(
            '/rest/v1/votos?evento_id=eq.' . urlencode($eventoId) . '&select=sentido'
        );

        $conteo = ['a_favor' => 0, 'en_contra' => 0];
        foreach ($votos as $v) {
            $conteo[$v['sentido']]++;
        }
        return $conteo;
    }

    public function yaVoto($eventoId, $usuarioId)
    {
        $resultado = $this->db->request(
            '/rest/v1/votos?evento_id=eq.' . urlencode($eventoId)
            . '&usuario_id=eq.' . urlencode($usuarioId) . '&select=id'
        );
        return !empty($resultado);
    }

    public function crear($eventoId, $usuarioId, $sentido)
    {
        return $this->db->request('/rest/v1/votos', 'POST', [
            'evento_id' => $eventoId,
            'usuario_id' => $usuarioId,
            'sentido' => $sentido,
        ]);
    }

    public function obtenerColectivosQueVotaron($eventoId)
    {
        $votos = $this->db->request(
            '/rest/v1/votos?evento_id=eq.' . urlencode($eventoId)
            . '&select=usuarios(usuario_roles(roles(nombre)))'
        );

        $colectivos = ['alumno' => false, 'docente' => false];
        foreach ($votos as $v) {
            $rolesDelVotante = $v['usuarios']['usuario_roles'] ?? [];
            foreach ($rolesDelVotante as $ur) {
                $nombreRol = $ur['roles']['nombre'] ?? null;
                if ($nombreRol === 'alumno') $colectivos['alumno'] = true;
                if ($nombreRol === 'docente') $colectivos['docente'] = true;
            }
        }
        return $colectivos;
    }
}