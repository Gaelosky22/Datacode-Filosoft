<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gafete — <?= htmlspecialchars($gafete['inscripciones']['eventos']['titulo']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/uadeo-theme.css">
</head>
<body class="gafete-page">
    <div class="gafete">
        <div class="gafete-emblema">DC</div>
        <p class="rol"><?= htmlspecialchars(ucfirst($gafete['tipo'])) ?></p>
        <h1><?= htmlspecialchars($gafete['inscripciones']['usuarios']['nombre']) ?></h1>
        <p class="gafete-dato"><?= htmlspecialchars($gafete['inscripciones']['eventos']['titulo']) ?></p>
        <p class="gafete-dato"><?= date('d/m/Y - H:i', strtotime($gafete['inscripciones']['eventos']['fecha_hora_inicio'])) ?></p>
        <p class="gafete-dato"><?= htmlspecialchars($gafete['inscripciones']['eventos']['ubicacion'] ?? '') ?></p>
        <p class="gafete-folio">Folio: <?= htmlspecialchars($gafete['folio']) ?></p>
        <button class="btn-imprimir" onclick="window.print()">Guardar como PDF</button>
    </div>
</body>
</html>