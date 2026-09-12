<div class="modal-card">
    <button class="modal-cerrar" onclick="cerrarModal()" aria-label="Cerrar">&times;</button>
    <span class="modal-emblem" aria-hidden="true"><img src="/assets/img/datacode-logo.png" alt=""></span>
    <h2><?= $vista === 'participare' ? 'Eventos en donde participaré' : 'Eventos en los que estoy inscrito' ?></h2>

    <div class="mis-eventos-lista">
        <?php if ($vista === 'participare'): ?>
            <?php
            $lista = array_filter($misInscripciones, function ($insc) use ($estadoInscripcion) {
                return !empty($estadoInscripcion[$insc['evento_id']]['gafete']);
            });
            ?>
            <?php if (empty($lista)): ?>
                <p class="mis-eventos-vacio">Aún no tienes participación asegurada en ningún evento.</p>
            <?php else: ?>
                <?php foreach ($lista as $insc): ?>
                    <div class="mis-evento-item">
                        <span class="mis-evento-titulo"><?= htmlspecialchars($insc['eventos']['titulo']) ?></span>
                        <span class="mis-evento-fecha"><?= date('d/m/Y - H:i', strtotime($insc['eventos']['fecha_hora_inicio'])) ?></span>
                        <a href="?r=alumno&accion=gafete&id=<?= htmlspecialchars($estadoInscripcion[$insc['evento_id']]['inscripcion_id']) ?>" target="_blank" class="card-link">Ver/Descargar gafete →</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php if (empty($misInscripciones)): ?>
                <p class="mis-eventos-vacio">Aún no estás inscrito en ningún evento.</p>
            <?php else: ?>
                <?php foreach ($misInscripciones as $insc): ?>
                    <div class="mis-evento-item">
                        <span class="mis-evento-titulo"><?= htmlspecialchars($insc['eventos']['titulo']) ?></span>
                        <span class="mis-evento-fecha"><?= date('d/m/Y - H:i', strtotime($insc['eventos']['fecha_hora_inicio'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
