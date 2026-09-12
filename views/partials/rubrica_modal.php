<div class="modal-card modal-card-ancho">
    <button class="modal-cerrar" onclick="cerrarRubricaModal()" aria-label="Cerrar">&times;</button>

    <span class="pill">Buildathon 2027</span>
    <h2>Data Code 2.0 — Sistemas digitales para la vida universitaria</h2>

    <section class="rubrica-bloque">
        <h3>1. Planteamiento del reto</h3>
        <p><strong>Objetivo:</strong> cada equipo debe identificar, elegir y digitalizar un proceso real de su propia Unidad Regional de la UAdeO (gestión de espacios, tutorías, servicio social, etc.) que actualmente se lleve de forma manual o informal, ofreciendo un flujo completo con persistencia de datos (no solo maquetas/demos de pantallas).</p>
        <p><strong>Justificación del cambio:</strong> promueve la utilidad orgánica adaptada a las necesidades reales de cada sede, permite evaluar procesos diversos mediante una sola rúbrica y ofrece continuidad usando a Data Code 2.0 como sistema de referencia.</p>

        <div class="rubrica-datos">
            <div class="rubrica-dato">
                <span>Formato y participantes</span>
                <span>Equipos de 3 a 5 estudiantes de Ingeniería en Software y carreras afines · 24 horas continuas de desarrollo.</span>
            </div>
            <div class="rubrica-dato">
                <span>Entregables</span>
                <span>Repositorio con código y README documentado, demo en vivo y defensa oral.</span>
            </div>
            <div class="rubrica-dato">
                <span>Cobertura territorial</span>
                <span>Unidades Regionales con carreras afines (ej. Guamúchil, Los Mochis, Culiacán, Mazatlán), con eliminatorias internas por sede y evento final presencial en la sede organizadora (Guamúchil).</span>
            </div>
            <div class="rubrica-dato">
                <span>Restricción técnica mínima</span>
                <span>El proyecto debe incluir al menos dos roles con permisos distintos, un flujo de más de un paso (más allá de un CRUD simple) y persistencia real en base de datos.</span>
            </div>
        </div>
    </section>

    <section class="rubrica-bloque">
        <h3>2. Estructura de la rúbrica (100 puntos totales)</h3>
        <p class="card-desc">Escala de desempeño: Sobresaliente 90–100% · Bueno 70–89% · Suficiente 50–69% · Insuficiente 0–49%.</p>

        <div class="rubrica-lista">
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Funcionamiento</span><span class="rubro-pts">25 pts</span></div>
                <p>Evalúa la demo en vivo del flujo completo, interacción real entre al menos dos roles y datos persistentes que sobrevivan recargas de página sin intervención manual en la BD.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Utilidad</span><span class="rubro-pts">20 pts</span></div>
                <p>Mide qué tan real y justificada es la problemática universitaria elegida de su unidad y el nivel de cobertura del proceso de punta a punta.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Calidad técnica</span><span class="rubro-pts">20 pts</span></div>
                <p>Valora la separación de responsabilidades en la arquitectura, seguridad básica (validación de permisos en servidor, consultas parametrizadas) y manejo seguro de credenciales.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Experiencia de uso</span><span class="rubro-pts">10 pts</span></div>
                <p>Evalúa la facilidad de navegación intuitiva, claridad en los mensajes y que cada rol acceda únicamente a sus funciones correspondientes.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Viabilidad y mantenimiento</span><span class="rubro-pts">10 pts</span></div>
                <p>Mide la calidad del archivo README, facilidad de instalación por parte de un tercero y documentación clara de dependencias.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Validación, documentación y defensa</span><span class="rubro-pts">10 pts</span></div>
                <p>Evalúa la capacidad del equipo para justificar sus decisiones de diseño, responder preguntas técnicas y ser honestos sobre el alcance real.</p>
            </div>
            <div class="rubro-item">
                <div class="rubro-encabezado"><span>Innovación</span><span class="rubro-pts">5 pts</span></div>
                <p>Premia la implementación de reglas de negocio propias y no obvias específicas del proceso (desempates, control de concurrencia, validación de horarios).</p>
            </div>
        </div>
    </section>

    <section class="rubrica-bloque">
        <h3>3. Criterios de evaluación y desempate</h3>
        <ul class="rubrica-notas">
            <li><strong>Puntaje final:</strong> se calcula mediante el promedio simple de las calificaciones de los jueces.</li>
            <li><strong>Desempate:</strong> primero por el puntaje obtenido en Funcionamiento; si persiste, se desempata por Utilidad.</li>
            <li><strong>Regla estricta:</strong> se evalúa únicamente el código que corra y funcione durante la demo en vivo — módulos o prototipos no operativos no reciben puntaje en Funcionamiento.</li>
        </ul>
    </section>
</div>
