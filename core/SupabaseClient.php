<?php

class SupabaseClient
{
    private $url;
    private $anonKey;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        $this->url = $config['url'];
        $this->anonKey = $config['anon_key'];
    }

    public function request(string $endpoint, string $metodo = 'GET', array $datos = [])
    {
        $ch = curl_init($this->url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);

        $headers = [
            'apikey: ' . $this->anonKey,
            'Authorization: Bearer ' . $this->anonKey,
            'Content-Type: application/json',
        ];

        if (in_array($metodo, ['POST', 'PATCH'])) {
            $headers[] = 'Prefer: return=representation';
        }

        if (!empty($datos)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $respuesta = curl_exec($ch);
        $error = curl_error($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //curl_close($ch);

        if ($error) {
            throw new Exception("Error al conectar con Supabase: $error");
        }
        if ($codigoHttp >= 400) {
            throw new Exception("Supabase respondió $codigoHttp: $respuesta");
        }

        return json_decode($respuesta, true) ?? [];
    }
}