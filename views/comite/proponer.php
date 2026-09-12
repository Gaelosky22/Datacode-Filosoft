<?php require __DIR__ . '/../header.php'; ?>

<section class="hero hero-secundario">
    <div class="container">
        <p class="hero-kicker">Portal de comité</p>
        <h1>Proponer un taller o evento</h1>
    </div>
</section>

<section class="eventos container">
    <?php if (!empty($error)): ?>
        <p class="mensaje-flash mensaje-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="form-propuesta-intro">
        <p class="eyebrow">NUEVA PROPUESTA</p>
        <h2>Cuéntanos sobre el evento</h2>
        <p>Completa la información para que el comité pueda evaluarla. Los campos con asterisco son obligatorios.</p>
    </div>

    <form method="POST" action="?r=comite&accion=guardarPropuesta" class="form-propuesta">

        <div class="form-fila">
            <div class="form-campo">
                <label for="titulo">Título *</label>
                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
            </div>
            <div class="form-campo">
                <label for="tipo">Tipo de evento *</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Selecciona</option>
                    <?php foreach (['taller' => 'Taller', 'curso' => 'Curso', 'hackathon' => 'Hackathon', 'buildathon' => 'Buildathon', 'torneo' => 'Torneo'] as $valor => $label): ?>
                        <option value="<?= $valor ?>" <?= (($_POST['tipo'] ?? '') === $valor) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-campo">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="sede_id">Sede *</label>
                <select name="sede_id" id="sede_id" required>
                    <option value="">Selecciona sede</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= htmlspecialchars($sede['id']) ?>"><?= htmlspecialchars($sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-campo">
                <label for="ubicacion">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" placeholder="Ej. Universidad de Guamuchil">
            </div>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="espacio" placeholder="Ej. Laboratorio de redes">Espacio</label>
                <input type="text" name="espacio" id="espacio">
            </div>
        </div>

        <div class="form-campo">
            <label>¿A qué otras sedes estará disponible?</label>
            <p class="form-ayuda">La sede organizadora que elegiste arriba siempre tiene acceso automático. Marca aquí si también quieres abrirlo a otras sedes.</p>
            <div class="checkbox-grid">
                <?php foreach ($sedes as $sede): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="sedes_permitidas[]" value="<?= htmlspecialchars($sede['id']) ?>">
                        <?= htmlspecialchars($sede['nombre']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="fecha_hora_inicio">Fecha y hora de inicio *</label>
                <input type="datetime-local" name="fecha_hora_inicio" id="fecha_hora_inicio" required>
            </div>
            <div class="form-campo">
                <label for="fecha_hora_fin">Fecha y hora de fin *</label>
                <input type="datetime-local" name="fecha_hora_fin" id="fecha_hora_fin" required>
            </div>
        </div>

        <div class="form-fila">
            <div class="form-campo">
                <label for="cupo">Cupo *</label>
                <input type="number" name="cupo" id="cupo" min="1" step="1" required>
            </div>
            <div class="form-campo">
                <label for="fecha_cierre_votacion">Cierre de votación *</label>
                <input type="datetime-local" name="fecha_cierre_votacion" id="fecha_cierre_votacion" required>
            </div>
        </div>

        <div class="form-campo">
            <label for="requisitos">Requisitos</label>
            <textarea name="requisitos" id="requisitos" rows="2"></textarea>
        </div>

        <div class="form-campo">
            <label for="costo_presupuestado">Costo presupuestado (MXN)</label>
            <input type="number" name="costo_presupuestado" id="costo_presupuestado" min="0" step="0.01" value="0">
        </div>

        <div class="form-checkbox">
            <input type="checkbox" name="tiene_cuota" id="tiene_cuota" onchange="document.getElementById('bloque-cuota').style.display = this.checked ? 'block' : 'none'">
            <label for="tiene_cuota">Este evento tiene cuota de inscripción</label>
        </div>
        <div class="form-campo" id="bloque-cuota" style="display:none;">
            <label for="cuota">Cuota de inscripción (MXN)</label>
            <input type="number" name="cuota" id="cuota" min="0" step="0.01" value="0">
        </div>

        <div class="form-checkbox">
            <input type="checkbox" name="es_por_equipos" id="es_por_equipos" onchange="document.getElementById('bloque-equipos').style.display = this.checked ? 'block' : 'none'">
            <label for="es_por_equipos">Este evento es por equipos</label>
        </div>
        <div class="form-fila" id="bloque-equipos" style="display:none;">
            <div class="form-campo">
                <label for="modalidad_equipos">Modalidad</label>
                <select name="modalidad_equipos" id="modalidad_equipos">
                    <option value="libre">Elección libre</option>
                    <option value="aleatoria">Asignación aleatoria</option>
                </select>
            </div>
            <div class="form-campo">
                <label for="tamano_equipo_max">Tamaño máximo por equipo</label>
                <input type="number" name="tamano_equipo_max" id="tamano_equipo_max" min="1" step="1">
            </div>
        </div>

        <button type="submit" class="btn-primario">Enviar propuesta al comité</button>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectSede = document.getElementById('sede_id');
    const checkboxes = document.querySelectorAll('input[name="sedes_permitidas[]"]');
    let checkboxSedeAnterior = null;

    function sincronizarCheckbox() {
        if (checkboxSedeAnterior) {
            checkboxSedeAnterior.disabled = false;
        }

        const valorActual = selectSede.value;
        checkboxes.forEach(function (checkbox) {
            if (checkbox.value === valorActual) {
                checkbox.checked = true;
                checkbox.disabled = true;
                checkboxSedeAnterior = checkbox;
            }
        });
    }

    selectSede.addEventListener('change', sincronizarCheckbox);

    // Bloqueo de fechas pasadas y coherencia inicio/fin/cierre de votación
    const ahora = new Date();
    ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
    const minimoActual = ahora.toISOString().slice(0, 16);

    const inicio = document.getElementById('fecha_hora_inicio');
    const fin = document.getElementById('fecha_hora_fin');
    const cierreVotacion = document.getElementById('fecha_cierre_votacion');

    inicio.min = minimoActual;
    fin.min = minimoActual;
    cierreVotacion.min = minimoActual;

    inicio.addEventListener('change', function () {
        fin.min = inicio.value;
        if (fin.value && fin.value < inicio.value) {
            fin.value = '';
        }
        cierreVotacion.max = inicio.value;
    });
});
</script>

<?php require __DIR__ . '/../footer.php'; ?>
