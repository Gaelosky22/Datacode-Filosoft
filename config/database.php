<?php
require_once __DIR__ . '/bootstrap.php';

$url = getenv('SUPABASE_URL');
$anonKey = getenv('SUPABASE_ANON_KEY');

if (!$url || !$anonKey) {
    throw new Exception(
        'Variables de entorno no configuradas. '
        . 'Copia .env.example a .env y configura los valores de Supabase.'
    );
}

return [
    'url' => $url,
    'anon_key' => $anonKey,
];