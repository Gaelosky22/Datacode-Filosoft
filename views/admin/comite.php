<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de administración</p>
        <h1>Gestión del comité</h1>
    </div>
</section>

<section class="eventos container">
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-flash mensaje-<?= $mensajeTipo ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="GET" action="" class="form-filtro-sede">
        <input type="hidden" name="r" value="admin">
        <input type="hidden" name="accion" value="comite">

        <div class="form-campo">
            <label for="sede_id">Sede</label>
            <select name="sede_id" id="sede_id" onchange="this.form.submit()">
                <option value="">Todas las sedes</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= htmlspecialchars($sede['id']) ?>" <?= ($sedeFiltro === $sede['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sede['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-campo">
            <label for="busqueda">Buscar por matrícula o nombre</label>
            <input type="text" name="busqueda" id="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ej. 24060101 o Ana">
        </div>

        <button type="submit" class="btn-primario btn-filtrar">Filtrar</button>
    </form>

    <div class="tabla-scroll" role="region" aria-label="Usuarios y roles" tabindex="0">
    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Matrícula</th>
                <th>Sede</th>
                <th>Roles</th>
                <th>Comité</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                    $rolesUsuario = array_map(function ($ur) {
                        return $ur['roles']['nombre'];
                    }, $usuario['usuario_roles'] ?? []);
                    $esComite = in_array('comite', $rolesUsuario);
                ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['matricula']) ?></td>
                    <td><?= htmlspecialchars($usuario['sedes']['nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars(implode(', ', $rolesUsuario)) ?></td>
                    <td>
                        <form method="POST" action="?r=admin&accion=toggleComite">
                            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['id']) ?>">
                            <input type="hidden" name="sede_id" value="<?= htmlspecialchars($sedeFiltro) ?>">
                            <input type="hidden" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>">
                            <?php if ($esComite): ?>
                                <input type="hidden" name="accion_toggle" value="quitar">
                                <button type="submit" class="btn-quitar-comite">Quitar del comité</button>
                            <?php else: ?>
                                <input type="hidden" name="accion_toggle" value="agregar">
                                <button type="submit" class="btn-agregar-comite">Agregar al comité</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
