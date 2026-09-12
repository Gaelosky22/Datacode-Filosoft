<?php
$tituloPagina = htmlspecialchars($evento['titulo']) . ' — Data Code';
require __DIR__ . '/header.php';
?>

<section class="container detalle-evento">
    <a href="/" class="detalle-volver">&larr; Volver a eventos</a>

    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
    <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
    <p class="detalle-descripcion"><?= htmlspecialchars($evento['descripcion']) ?></p>

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
</section>

<?php require __DIR__ . '/footer.php'; ?>
