<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de comité</p>
        <h1>Panel de gestión de eventos</h1>
    </div>
</section>

<section class="eventos container">
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-flash mensaje-<?= $mensajeTipo ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <div class="comite-secciones">

        <?php if ($esDocente): ?>
            <a href="?r=comite&accion=proponer" class="comite-tarjeta">
                <h3>Proponer un taller o evento</h3>
                <p class="card-desc">Crea una propuesta para que el comité la revise y vote.</p>
            </a>
        <?php endif; ?>

        <?php if ($esComite): ?>
            <article class="comite-tarjeta">
                <h3>Propuestas por votar</h3>
                <p class="card-desc">Revisa, comenta y vota las propuestas pendientes.</p>
                <span class="badge-proximamente">Próximamente</span>
            </article>
            <article class="comite-tarjeta">
                <h3>Presupuesto de eventos</h3>
                <p class="card-desc">Consulta el desglose de costos y la proyección de ingresos.</p>
                <span class="badge-proximamente">Próximamente</span>
            </article>
        <?php endif; ?>

        <?php if ($esCoordinacion): ?>
            <article class="comite-tarjeta">
                <h3>Publicar eventos aprobados</h3>
                <p class="card-desc">Registra la resolución de una votación y publica el evento.</p>
                <span class="badge-proximamente">Próximamente</span>
            </article>
        <?php endif; ?>

        <?php if ($esAdministrador): ?>
            <article class="comite-tarjeta">
                <h3>Gestión de usuarios y roles</h3>
                <p class="card-desc">Asigna quién pertenece al comité y administra permisos.</p>
                <span class="badge-proximamente">Próximamente</span>
            </article>
        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>