<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Sede.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../core/SupabaseClient.php';

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

        header('HX-Redirect: /');
    }

    public function registro()
    {
        $sedeModel = new Sede();
        $sedes = $sedeModel->obtenerTodas();
        require __DIR__ . '/../views/partials/registro_modal.php';
    }

    public function registrar()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');
        $sedeId = $_POST['sede_id'] ?? '';
        $turno = $_POST['turno'] ?? '';

        $sedeModel = new Sede();
        $sedes = $sedeModel->obtenerTodas();

        if ($nombre === '' || $matricula === '' || $contrasena === '' || $sedeId === '' || $turno === '') {
            $error = 'Completa todos los campos obligatorios.';
            require __DIR__ . '/../views/partials/registro_modal.php';
            return;
        }

        $usuarioModel = new Usuario();
        $existente = $usuarioModel->obtenerPorMatricula($matricula);

        if ($existente) {
            $error = 'Esa matrícula ya está registrada.';
            require __DIR__ . '/../views/partials/registro_modal.php';
            return;
        }

        $nuevoUsuario = $usuarioModel->crear([
            'nombre' => $nombre,
            'matricula' => $matricula,
            'correo' => $correo,
            'contrasena' => $contrasena,
            'sede_id' => $sedeId,
            'turno' => $turno,
        ]);

        $usuarioId = $nuevoUsuario[0]['id'] ?? null;

        if (!$usuarioId) {
            $error = 'No se pudo completar el registro. Intenta de nuevo.';
            require __DIR__ . '/../views/partials/registro_modal.php';
            return;
        }

        $rolModel = new Rol();
        $rolAlumnoId = $rolModel->obtenerIdPorNombre('alumno');

        if ($rolAlumnoId) {
            $db = new SupabaseClient();
            $db->request('/rest/v1/usuario_roles', 'POST', [
                'usuario_id' => $usuarioId,
                'rol_id' => $rolAlumnoId,
            ]);
        }

        $_SESSION['usuario'] = [
            'id' => $usuarioId,
            'nombre' => $nombre,
            'matricula' => $matricula,
            'sede_id' => $sedeId,
            'roles' => ['alumno'],
        ];

        header('HX-Redirect: /');
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}