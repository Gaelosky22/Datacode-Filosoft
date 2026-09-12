<?php require __DIR__ . '/header.php'; ?>

<?php if (!empty($error)): ?>
    <p class="mensaje-error">Error: <?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<section class="hero">
    <div class="container hero-layout">
        <div class="hero-copy">
        <p class="hero-kicker">Portal oficial de eventos</p>
        <h1>El punto de encuentro para talleres y eventos de Ingeniería en Software</h1>
        <p class="hero-lead">
            Consulta lo que se organiza en tu sede, inscríbete en un par de clics
            y lleva tu gafete el día del evento. Todo en un solo lugar.
        </p>
        <a class="hero-action" href="#eventos">Explorar eventos <span aria-hidden="true">↗</span></a>
        <p class="hero-note">Aprende. Conecta. Comparte.</p>
        </div>
        <div class="hero-art" aria-hidden="true">
            <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
            <span class="art-caption">COMUNIDAD · SOFTWARE · IDEAS</span>
            <img class="art-logo" src="/assets/img/datacode-logo.png" alt="">
            <div class="art-label"><span class="art-dot"></span> El siguiente paso empieza aquí</div>
            <span class="art-index">DATA CODE / UAdeO</span>
        </div>
    </div>
</section>

<section class="container reto-seccion">
    <div class="card-reto" role="button" tabindex="0" onclick="abrirRubricaModal()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();abrirRubricaModal();}">
        <span class="pill">Buildathon 2027</span>
        <h3>Data Code 2.0 — Sistemas digitales para la vida universitaria</h3>
        <p class="card-desc">
            Digitaliza un proceso real de tu Unidad Regional en 24 horas continuas: equipos de
            3 a 5 integrantes, al menos dos roles con permisos distintos y persistencia real en
            base de datos. Conoce el planteamiento completo y la rúbrica de 100 puntos.
        </p>
        <span class="card-link">Ver planteamiento y rúbrica completa →</span>
    </div>
</section>

<div class="modal-overlay" id="rubricaOverlay" onclick="if(event.target === this) cerrarRubricaModal()">
    <?php include __DIR__ . '/partials/rubrica_modal.php'; ?>
</div>

<section class="eventos container" id="eventos">
    <div class="section-heading"><div><p class="eyebrow">ENCUENTRA TU PRÓXIMA EXPERIENCIA</p><h2>Próximos eventos y talleres</h2></div><span class="section-note">Espacios para seguir aprendiendo</span></div>
    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>Por el momento no hay eventos publicados.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <a class="card-evento" href="?r=inicio&accion=detalle&id=<?= urlencode($evento['id']) ?>"
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
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
