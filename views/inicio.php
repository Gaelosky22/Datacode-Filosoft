<?php require __DIR__ . '/header.php'; ?>

<?php if (!empty($error)): ?>
    <p class="mensaje-error">Error: <?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<section class="hero">
    <div class="container">
        <p class="hero-kicker">Portal oficial de eventos</p>
        <h1>El punto de encuentro para talleres y eventos de Ingeniería en Software</h1>
        <p class="hero-lead">
            Consulta lo que se organiza en tu sede, inscríbete en un par de clics
            y lleva tu gafete el día del evento. Todo en un solo lugar.
        </p>
    </div>
</section>

<section class="eventos container" id="eventos">
    <h2>Próximos eventos y talleres</h2>
    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>Por el momento no hay eventos publicados.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <div class="card-evento"
                     hx-get="?r=inicio&accion=detalleModal&id=<?= urlencode($evento['id']) ?>"
                     hx-target="#modalContenido"
                     hx-swap="innerHTML">
                    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
                    <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                    <p class="card-meta">
                        <?= htmlspecialchars($evento['sedes']['nombre'] ?? 'Sede sin definir') ?>
                        ·
                        <?= date('d/m/Y - H:i', strtotime($evento['fecha_hora_inicio'])) ?>
                    </p>
                    <p class="card-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                    <div class="card-footer">
                        <span class="card-cupo"><?= (int)$evento['cupo'] ?> lugares</span>
                        <span class="card-link">Ver detalles →</span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>