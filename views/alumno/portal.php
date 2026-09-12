<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de alumnos</p>
        <h1>Talleres y eventos disponibles en tu sede</h1>
    </div>
</section>

<section class="mis-eventos container" id="misEventos">
    <h2 id="misEventosTitulo">Eventos en los que estoy inscrito</h2>

    <div class="mis-eventos-lista" id="listaInscrito">
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
    </div>

    <div class="mis-eventos-lista" id="listaParticipare" hidden>
        <?php
        $misEventosConfirmados = array_filter($misInscripciones, function ($insc) use ($estadoInscripcion) {
            return !empty($estadoInscripcion[$insc['evento_id']]['gafete']);
        });
        ?>
        <?php if (empty($misEventosConfirmados)): ?>
            <p class="mis-eventos-vacio">Aún no tienes participación asegurada en ningún evento.</p>
        <?php else: ?>
            <?php foreach ($misEventosConfirmados as $insc): ?>
                <div class="mis-evento-item">
                    <span class="mis-evento-titulo"><?= htmlspecialchars($insc['eventos']['titulo']) ?></span>
                    <span class="mis-evento-fecha"><?= date('d/m/Y - H:i', strtotime($insc['eventos']['fecha_hora_inicio'])) ?></span>
                    <a href="?r=alumno&accion=gafete&id=<?= htmlspecialchars($estadoInscripcion[$insc['evento_id']]['inscripcion_id']) ?>" target="_blank" class="card-link">Ver/Descargar gafete →</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="eventos container">
    <?php if ($mensaje): ?>
        <p class="mensaje-flash mensaje-<?= $mensajeTipo ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <div class="eventos-grid">
        <?php if (empty($eventos)): ?>
            <p>No hay eventos disponibles en tu sede por el momento.</p>
        <?php else: ?>
            <?php foreach ($eventos as $evento): ?>
                <div class="card-evento card-evento-alumno">
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

                        <?php $estado = $estadoInscripcion[$evento['id']] ?? null; ?>

                        <?php if (!$estado): ?>
                            <?php if ($esAlumno): ?>
                                <form method="POST" action="?r=alumno&accion=inscribir">
                                    <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
                                    <button type="submit" class="btn-inscribir">Inscribirme</button>
                                </form>
                            <?php else: ?>
                                <span class="badge-solo-alumnos">Solo alumnos pueden inscribirse</span>
                            <?php endif; ?>

                        <?php elseif ($evento['tiene_cuota'] && !$estado['pago']): ?>
                            <form method="POST" action="?r=alumno&accion=simularPago">
                                <input type="hidden" name="inscripcion_id" value="<?= htmlspecialchars($estado['inscripcion_id']) ?>">
                                <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
                                <button type="submit" class="btn-inscribir">Simular pago ($<?= number_format($evento['cuota'], 2) ?>)</button>
                            </form>

                        <?php elseif ($estado['gafete']): ?>
                            <a href="?r=alumno&accion=gafete&id=<?= htmlspecialchars($estado['inscripcion_id']) ?>" target="_blank" class="card-link">Descargar gafete →</a>

                        <?php else: ?>
                            <span class="badge-inscrito">Ya inscrito</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>