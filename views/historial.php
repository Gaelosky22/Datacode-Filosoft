<?php require __DIR__ . '/header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Historial</p>
        <h1>Eventos y talleres pasados</h1>
    </div>
</section>

<section class="eventos container">
    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>Todavía no hay eventos pasados registrados.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <div class="card-evento card-evento-pasado">
                    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
                    <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                    <p class="card-meta">
                        <?= htmlspecialchars($evento['sedes']['nombre'] ?? 'Sede sin definir') ?>
                        ·
                        <?= date('d/m/Y', strtotime($evento['fecha_hora_inicio'])) ?>
                    </p>
                    <p class="card-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>