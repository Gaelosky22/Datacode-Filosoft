<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Gestión de equipos</p>
        <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
    </div>
</section>

<section class="eventos container">
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-flash mensaje-<?= $mensajeTipo ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <h2 class="comentarios-titulo">Crear equipo</h2>
    <form method="POST" action="?r=equipo&accion=crear" class="form-propuesta">
        <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
        <div class="form-fila">
            <div class="form-campo">
                <label for="nombre">Nombre del equipo</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-campo">
                <label for="tamano_max">Tamaño máximo</label>
                <input type="number" name="tamano_max" id="tamano_max" min="1" step="1" required>
            </div>
        </div>
        <button type="submit" class="btn-primario">Crear equipo</button>
    </form>

    <h2 class="comentarios-titulo">Equipos existentes</h2>
    <?php if (empty($equipos)): ?>
        <p class="card-desc">Todavía no hay equipos creados.</p>
    <?php else: ?>
        <div class="eventos-grid">
            <?php foreach ($equipos as $eq): ?>
                <div class="card-evento">
                    <h3><?= htmlspecialchars($eq['nombre']) ?></h3>
                    <p class="card-meta"><?= $eq['ocupados'] ?> / <?= $eq['tamano_max'] ?> lugares</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../footer.php'; ?>