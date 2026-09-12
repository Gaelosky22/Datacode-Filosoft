<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker"><?= htmlspecialchars($evento['sedes']['nombre'] ?? '') ?></p>
        <h1>Resolver: <?= htmlspecialchars($evento['titulo']) ?></h1>
    </div>
</section>

<section class="eventos container detalle-propuesta">
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-flash mensaje-<?= $mensajeTipo ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <div class="votacion-tally">
        <div class="tally-barra">
            <div class="tally-favor" style="flex-grow: <?= max($conteo['a_favor'], 1) ?>;"></div>
            <div class="tally-contra" style="flex-grow: <?= max($conteo['en_contra'], 1) ?>;"></div>
        </div>
        <p><strong><?= $conteo['a_favor'] ?></strong> a favor · <strong><?= $conteo['en_contra'] ?></strong> en contra</p>
        <p class="card-meta">Cierre programado: <?= $evento['fecha_cierre_votacion'] ? date('d/m/Y H:i', strtotime($evento['fecha_cierre_votacion'])) : 'Sin definir' ?> — puedes resolver antes si ya es claro</p>
    </div>

    <h2 class="comentarios-titulo">Comentarios del comité</h2>
    <div class="comentarios-lista">
        <?php if (empty($comentarios)): ?>
            <p class="card-desc">No hay comentarios todavía.</p>
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

    <h2 class="comentarios-titulo">Editar propuesta antes de decidir</h2>
    <form method="POST" action="?r=admin&accion=guardarEdicion" class="form-propuesta">
        <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">

        <div class="form-campo">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($evento['titulo']) ?>">
        </div>

        <div class="form-campo">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3"><?= htmlspecialchars($evento['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="ubicacion">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($evento['ubicacion'] ?? '') ?>">
            </div>
            <div class="form-campo">
                <label for="espacio">Espacio</label>
                <input type="text" name="espacio" id="espacio" value="<?= htmlspecialchars($evento['espacio'] ?? '') ?>">
            </div>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="cupo">Cupo</label>
                <input type="number" name="cupo" id="cupo" min="1" step="1" value="<?= (int)$evento['cupo'] ?>">
            </div>
            <div class="form-campo">
                <label for="costo_presupuestado">Costo presupuestado (MXN)</label>
                <input type="number" name="costo_presupuestado" id="costo_presupuestado" min="0" step="0.01" value="<?= $evento['costo_presupuestado'] ?>">
            </div>
        </div>

        <div class="form-checkbox">
            <input type="checkbox" name="tiene_cuota" id="tiene_cuota" <?= $evento['tiene_cuota'] ? 'checked' : '' ?> onchange="document.getElementById('bloque-cuota').style.display = this.checked ? 'block' : 'none'">
            <label for="tiene_cuota">Tiene cuota de inscripción</label>
        </div>
        <div class="form-campo" id="bloque-cuota" style="<?= $evento['tiene_cuota'] ? '' : 'display:none;' ?>">
            <label for="cuota">Cuota (MXN)</label>
            <input type="number" name="cuota" id="cuota" min="0" step="0.01" value="<?= $evento['cuota'] ?>">
        </div>

        <div class="form-campo">
            <label for="requisitos">Requisitos</label>
            <textarea name="requisitos" id="requisitos" rows="2"><?= htmlspecialchars($evento['requisitos'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-primario">Guardar cambios</button>
    </form>

    <h2 class="comentarios-titulo">Resolución final</h2>
    <div class="resolucion-botones">
        <form method="POST" action="?r=admin&accion=aprobar" onsubmit="return confirm('¿Aprobar y publicar esta propuesta ahora mismo?');">
            <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
            <button type="submit" class="btn-voto btn-voto-favor">Aprobar y publicar</button>
        </form>
        <form method="POST" action="?r=admin&accion=rechazar" onsubmit="return confirm('¿Rechazar esta propuesta?');">
            <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['id']) ?>">
            <button type="submit" class="btn-voto btn-voto-contra">Rechazar propuesta</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>