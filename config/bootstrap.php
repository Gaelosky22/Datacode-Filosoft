<?php
// Cargar variables de .env si existe
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Saltar comentarios
        if (strpos($line, '=') === false) continue; // Saltar líneas sin =

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}
