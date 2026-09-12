<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de administración</p>
        <h1>Propuestas pendientes de resolución</h1>
    </div>
</section>

<section class="eventos container">
    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>No hay propuestas pendientes en ninguna sede.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <a href="?r=admin&accion=resolver&id=<?= urlencode($evento['id']) ?>" class="card-evento">
                    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
                    <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                    <p class="card-meta">
                        <?= htmlspecialchars($evento['sedes']['nombre'] ?? '') ?>
                        ·
                        Cierra: <?= $evento['fecha_cierre_votacion'] ? date('d/m/Y H:i', strtotime($evento['fecha_cierre_votacion'])) : 'Sin definir' ?>
                    </p>
                    <p class="card-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>