<?php
class Auth
{
    public static function requerirLogin()
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: /');
            exit;
        }
    }

    public static function requerirRol($rol)
    {
        self::requerirLogin();
        if (!in_array($rol, $_SESSION['usuario']['roles'] ?? [])) {
            http_response_code(403);
            echo "No autorizado para acceder a esta sección.";
            exit;
        }
    }
}