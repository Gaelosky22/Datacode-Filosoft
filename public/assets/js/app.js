document.addEventListener('DOMContentLoaded', function () {
    const boton = document.getElementById('navToggle');
    const nav = document.getElementById('siteNav');

    if (boton && nav) {
        boton.addEventListener('click', function () {
            const abierto = nav.classList.toggle('abierto');
            boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
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

// Menú kebab del portal de alumnos (Mis eventos)
document.addEventListener('DOMContentLoaded', function () {
    const kebabBtn = document.getElementById('kebabBtn');
    const kebabDropdown = document.getElementById('kebabDropdown');
    const kebabMenu = document.getElementById('kebabMenu');

    if (!kebabBtn || !kebabDropdown) return;

    const titulos = {
        inscrito: 'Eventos en los que estoy inscrito',
        participare: 'Eventos en donde participaré',
    };

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
        opcion.addEventListener('click', function () {
            const vista = opcion.dataset.vista;
            const listaInscrito = document.getElementById('listaInscrito');
            const listaParticipare = document.getElementById('listaParticipare');
            const misEventosTitulo = document.getElementById('misEventosTitulo');
            const misEventos = document.getElementById('misEventos');

            if (listaInscrito && listaParticipare) {
                listaInscrito.hidden = vista !== 'inscrito';
                listaParticipare.hidden = vista !== 'participare';
            }
            if (misEventosTitulo && titulos[vista]) {
                misEventosTitulo.textContent = titulos[vista];
            }

            kebabDropdown.querySelectorAll('.kebab-opcion').forEach(function (op) {
                op.classList.toggle('activa', op === opcion);
            });

            cerrarKebab();

            if (misEventos) misEventos.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});