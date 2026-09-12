<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker"><?= htmlspecialchars($evento['sedes']['nombre'] ?? '') ?></p>
        <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
    </div>
</section>

<section class="eventos container detalle-propuesta">
    <?php if (!empty($error)): ?>
        <p class="mensaje-flash mensaje-<?= $errorTipo ?>"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p class="card-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>

    <div class="detalle-lista">
        <div class="detalle-fila"><span>Cupo</span><span><?= (int)$evento['cupo'] ?></span></div>
        <div class="detalle-fila"><span>Fecha</span><span><?= date('d/m/Y H:i', strtotime($evento['fecha_hora_inicio'])) ?></span></div>
        <div class="detalle-fila"><span>Costo presupuestado</span><span>$<?= number_format($evento['costo_presupuestado'], 2) ?></span></div>
        <div class="detalle-fila"><span>Cuota</span><span><?= $evento['tiene_cuota'] ? '$' . number_format($evento['cuota'], 2) : 'Gratuito' ?></span></div>
    </div>

    <div class="votacion-timer" data-cierre="<?= htmlspecialchars($evento['fecha_cierre_votacion']) ?>">
        <span id="timer-texto">Calculando tiempo restante…</span>
    </div>

    <div class="votacion-tally">
        <div class="tally-barra">
            <div class="tally-favor" style="flex-grow: <?= max($conteo['a_favor'], 1) ?>;"></div>
            <div class="tally-contra" style="flex-grow: <?= max($conteo['en_contra'], 1) ?>;"></div>
        </div>
        <p><strong><?= $conteo['a_favor'] ?></strong> a favor · <strong><?= $conteo['en_contra'] ?></strong> en contra</p>
    </div>

    <?php if ($votacionAbierta && !$yaVoto): ?>
        <form method="POST" action="?r=comite&accion=votar" class="votacion-botones">
            <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
            <button type="submit" name="sentido" value="a_favor" class="btn-voto btn-voto-favor">Votar a favor</button>
            <button type="submit" name="sentido" value="en_contra" class="btn-voto btn-voto-contra">Votar en contra</button>
        </form>
    <?php elseif ($yaVoto): ?>
        <p class="badge-inscrito">Ya emitiste tu voto en esta propuesta.</p>
    <?php else: ?>
        <p class="badge-inscrito">La votación para esta propuesta ya cerró.</p>
    <?php endif; ?>

    <h2 class="comentarios-titulo">Discusión</h2>

    <form method="POST" action="?r=comite&accion=comentar" class="form-comentario">
        <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
        <textarea name="texto" rows="2" placeholder="Escribe un comentario para el comité..." required></textarea>
        <button type="submit" class="btn-primario">Comentar</button>
    </form>

    <div class="comentarios-lista">
        <?php if (empty($comentarios)): ?>
            <p class="card-desc">Todavía no hay comentarios.</p>
        <?php else: ?>
            <?php foreach ($comentarios as $c): ?>
                <div class="comentario-item">
                    <p class="comentario-autor"><?= htmlspecialchars($c['usuarios']['nombre'] ?? 'Usuario') ?></p>
                    <p class="comentario-texto"><?= htmlspecialchars($c['texto']) ?></p>
                    <p class="comentario-fecha"><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.querySelector('.votacion-timer');
    const texto = document.getElementById('timer-texto');
    const cierre = new Date(contenedor.dataset.cierre).getTime();

    function actualizar() {
        const restante = cierre - Date.now();
        if (restante <= 0) {
            texto.textContent = 'La votación ha cerrado.';
            return;
        }
        const dias = Math.floor(restante / 86400000);
        const horas = Math.floor((restante % 86400000) / 3600000);
        const minutos = Math.floor((restante % 3600000) / 60000);
        texto.textContent = 'Cierra en ' + dias + 'd ' + horas + 'h ' + minutos + 'm';
    }

    actualizar();
    setInterval(actualizar, 60000);
});
</script>

<?php require __DIR__ . '/../footer.php'; ?>