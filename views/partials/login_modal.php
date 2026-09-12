<div class="modal-card modal-login">
    <button class="modal-cerrar" onclick="cerrarModal()" aria-label="Cerrar">&times;</button>
    <h2>Ingresar</h2>
    <p class="modal-desc">Usa tu matrícula y contraseña de Data Code.</p>

    <?php if (!empty($error)): ?>
        <p class="mensaje-error mensaje-error-modal"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form hx-post="?r=auth&accion=autenticar" hx-target="#modalContenido" hx-swap="innerHTML" class="form-login">
        <label for="matricula">Matrícula</label>
        <input type="text" name="matricula" id="matricula" required autofocus>

        <label for="contrasena">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <button type="submit" class="btn-primario">Ingresar</button>
    </form>
</div>