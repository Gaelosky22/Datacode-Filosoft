<div class="modal-card">
    <button class="modal-cerrar" onclick="cerrarModal()" aria-label="Cerrar">&times;</button>
    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
    <h2><?= htmlspecialchars($evento['titulo']) ?></h2>
    <p class="modal-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>

    <div class="detalle-lista">
        <div class="detalle-fila">
            <span>Sede</span>
            <span><?= htmlspecialchars($evento['sedes']['nombre'] ?? 'Sin definir') ?></span>
        </div>
        <div class="detalle-fila">
            <span>Ubicación</span>
            <span><?= htmlspecialchars($evento['ubicacion'] ?? 'Por confirmar') ?></span>
        </div>
        <div class="detalle-fila">
            <span>Fecha y hora</span>
            <span><?= date('d/m/Y - H:i', strtotime($evento['fecha_hora_inicio'])) ?></span>
        </div>
        <div class="detalle-fila">
            <span>Responsable</span>
            <span><?= htmlspecialchars($evento['responsable']['nombre'] ?? 'Sin asignar') ?></span>
        </div>
        <div class="detalle-fila">
            <span>Cupo</span>
            <span><?= (int)$evento['cupo'] ?> lugares</span>
        </div>
        <div class="detalle-fila">
            <span>Costo</span>
            <span><?= $evento['tiene_cuota'] ? '$' . number_format($evento['cuota'], 2) : 'Gratuito' ?></span>
        </div>
    </div>
</div>