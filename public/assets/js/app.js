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