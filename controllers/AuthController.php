<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public function login()
    {
        require __DIR__ . '/../views/partials/login_modal.php';
    }

    public function autenticar()
    {
        $matricula = trim($_POST['matricula'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        if ($matricula === '' || $contrasena === '') {
            $error = 'Ingresa tu matrícula y contraseña.';
            require __DIR__ . '/../views/partials/login_modal.php';
            return;
        }

        $modelo = new Usuario();
        $usuario = $modelo->obtenerPorMatricula($matricula);

        if (!$usuario || $usuario['contrasena'] !== $contrasena) {
            $error = 'Matrícula o contraseña incorrecta.';
            require __DIR__ . '/../views/partials/login_modal.php';
            return;
        }

        $roles = array_map(function ($ur) {
            return $ur['roles']['nombre'];
        }, $usuario['usuario_roles'] ?? []);

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'matricula' => $usuario['matricula'],
            'sede_id' => $usuario['sede_id'],
            'roles' => $roles,
        ];

        // HTMX intercepta este header y hace la redirección completa por nosotros
        header('HX-Redirect: /');
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}