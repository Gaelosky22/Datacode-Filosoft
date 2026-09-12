<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de comité</p>
        <h1>Propuestas por votar</h1>
    </div>
</section>

<section class="eventos container">
    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>No hay propuestas pendientes para tu sede por el momento.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <a href="?r=comite&accion=propuesta&id=<?= urlencode($evento['id']) ?>" class="card-evento">
                    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
                    <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                    <p class="card-meta">
                        <?= htmlspecialchars($evento['sedes']['nombre'] ?? 'Sede sin definir') ?>
                        ·
                        Cierra: <?= date('d/m/Y H:i', strtotime($evento['fecha_cierre_votacion'])) ?>
                    </p>
                    <p class="card-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>