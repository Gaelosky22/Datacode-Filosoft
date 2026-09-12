<div class="modal-card modal-login">
    <button class="modal-cerrar" onclick="cerrarModal()" aria-label="Cerrar">&times;</button>
    <h2>Crear cuenta</h2>
    <p class="modal-desc">Regístrate con tu matrícula de alumno.</p>

    <?php if (!empty($error)): ?>
        <p class="mensaje-error mensaje-error-modal"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form hx-post="?r=auth&accion=registrar" hx-target="#modalContenido" hx-swap="innerHTML" class="form-login">
        <label for="nombre">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>

        <label for="matricula">Matrícula</label>
        <input type="text" name="matricula" id="matricula" value="<?= htmlspecialchars($_POST['matricula'] ?? '') ?>" required>

        <label for="correo">Correo (opcional)</label>
        <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">

        <label for="sede_id">Sede</label>
        <select name="sede_id" id="sede_id" required>
            <option value="">Selecciona tu sede</option>
            <?php foreach ($sedes as $sede): ?>
                <option value="<?= htmlspecialchars($sede['id']) ?>" <?= (($_POST['sede_id'] ?? '') === $sede['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sede['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="turno">Turno</label>
        <select name="turno" id="turno" required>
            <option value="">Selecciona tu turno</option>
            <option value="matutino" <?= (($_POST['turno'] ?? '') === 'matutino') ? 'selected' : '' ?>>Matutino</option>
            <option value="vespertino" <?= (($_POST['turno'] ?? '') === 'vespertino') ? 'selected' : '' ?>>Vespertino</option>
        </select>

        <label for="contrasena">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <button type="submit" class="btn-primario">Crear cuenta</button>
    </form>

    <p class="modal-alt-accion">
        ¿Ya tienes cuenta?
        <a href="#" hx-get="?r=auth&accion=login" hx-target="#modalContenido" hx-swap="innerHTML" onclick="return false;">Inicia sesión</a>
    </p>
</div>