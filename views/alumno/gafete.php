<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gafete — <?= htmlspecialchars($gafete['inscripciones']['eventos']['titulo']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    <style>
        body { font-family: system-ui, sans-serif; background: #FAF7EE; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .gafete { background:#FFFFFF; border:2px solid #9D2C53; border-radius:14px; width:340px; padding:2rem; text-align:center; }
        .gafete-emblema { width:56px; height:56px; border-radius:50%; background:#9D2C53; color:#FFF; display:flex; align-items:center; justify-content:center; font-family:"Fraunces",serif; font-weight:600; margin:0 auto 1rem; }
        .gafete h1 { font-family:"Fraunces",serif; font-size:1.3rem; margin:0 0 .3rem; color:#211E1C; }
        .gafete .rol { text-transform:uppercase; font-size:.75rem; letter-spacing:.05em; color:#9D2C53; font-weight:600; margin-bottom:1.25rem; }
        .gafete-dato { font-size:.85rem; color:#6B655A; margin:.3rem 0; }
        .gafete-folio { margin-top:1.5rem; padding-top:1rem; border-top:1px dashed #C3BCB1; font-family:"Fraunces",serif; font-size:1.1rem; color:#211E1C; }
        .btn-imprimir { margin-top:1.5rem; background:#9D2C53; color:#FFF; border:none; padding:.6rem 1.2rem; border-radius:6px; cursor:pointer; font-size:.9rem; }
        @media print { .btn-imprimir { display:none; } body { background:#FFF; } }
    </style>
</head>
<body>
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