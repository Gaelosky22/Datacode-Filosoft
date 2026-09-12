<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($evento['titulo']) ?> — Data Code</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/pico.min.css">
    <link rel="stylesheet" href="/assets/css/uadeo-theme.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="wordmark">Data Code<span class="wordmark-dot">.</span></a>
    </div>
</header>

<main class="container detalle-evento">
    <a href="/" class="detalle-volver">&larr; Volver a eventos</a>

    <span class="pill pill-<?= strtolower($evento['tipo']) ?>"><?= ucfirst($evento['tipo']) ?></span>
    <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
    <p class="hero-lead"><?= htmlspecialchars($evento['descripcion']) ?></p>

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
</main>

<footer class="site-footer container">
    <p>Data Code — UAdeO Unidad Regional Guamúchil</p>
</footer>

</body>
</html>