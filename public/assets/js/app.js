document.addEventListener('DOMContentLoaded', function () {
    const boton = document.getElementById('navToggle');
    const nav = document.getElementById('siteNav');

    if (boton && nav) {
        function cerrarNavegacion() {
            nav.classList.remove('abierto');
            boton.setAttribute('aria-expanded', 'false');
            boton.setAttribute('aria-label', 'Abrir menú');
        }

        boton.addEventListener('click', function () {
            const abierto = nav.classList.toggle('abierto');
            boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            boton.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
        });

        nav.querySelectorAll('a').forEach(function (enlace) {
            enlace.addEventListener('click', cerrarNavegacion);
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1050) cerrarNavegacion();
        });
    }
});

// Cuando HTMX termina de meter el fragmento del detalle en el modal, lo mostramos
document.addEventListener('htmx:afterSwap', function (e) {
    if (e.detail.target && e.detail.target.id === 'modalContenido') {
        document.getElementById('modalOverlay').classList.add('abierto');
    }
});

function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('abierto');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarModal();
});

// Tarjeta de "Data Code 2.0 — Buildathon 2027" en el inicio
function abrirRubricaModal() {
    const overlay = document.getElementById('rubricaOverlay');
    if (overlay) overlay.classList.add('abierto');
}

function cerrarRubricaModal() {
    const overlay = document.getElementById('rubricaOverlay');
    if (overlay) overlay.classList.remove('abierto');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarRubricaModal();
});

// Menú kebab de "Mis eventos" (rol alumno, en el header de todo el sitio)
document.addEventListener('DOMContentLoaded', function () {
    const kebabBtn = document.getElementById('kebabBtn');
    const kebabDropdown = document.getElementById('kebabDropdown');
    const kebabMenu = document.getElementById('kebabMenu');

    if (!kebabBtn || !kebabDropdown) return;

    function cerrarKebab() {
        kebabDropdown.classList.remove('abierto');
        kebabBtn.setAttribute('aria-expanded', 'false');
    }

    kebabBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const abierto = kebabDropdown.classList.toggle('abierto');
        kebabBtn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });

    document.addEventListener('click', function (e) {
        if (kebabMenu && !kebabMenu.contains(e.target)) cerrarKebab();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarKebab();
    });

    kebabDropdown.querySelectorAll('.kebab-opcion').forEach(function (opcion) {
        opcion.addEventListener('click', cerrarKebab);
    });
});
