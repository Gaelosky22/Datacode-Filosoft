<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?? 'Data Code — UAdeO Guamúchil' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/uadeo-theme.css">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <a href="/" class="brand">
            <span class="brand-emblem">DC</span>
            <span class="brand-text">
                <span class="brand-name">Data Code</span>
                <span class="brand-sub">UAdeO · Unidad Regional Guamúchil</span>
            </span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <nav class="site-nav" id="siteNav">
            <a href="/">Inicio</a>
            <a href="#eventos">Talleres y eventos</a>
            <a href="?r=inicio&accion=historial">Eventos pasados</a>
            <a href="?r=comite&accion=index">Comité</a>
            <a href="?r=auth&accion=login" class="nav-cta">Ingresar</a>
        </nav>
    </div>
</header>

<main>