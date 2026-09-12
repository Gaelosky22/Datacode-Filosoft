-- =========================================================
-- DATA CODE — Script consolidado: esquema completo + datos de prueba
-- Seguro de volver a correr (usa IF NOT EXISTS / ON CONFLICT).
-- Pegar completo en el SQL Editor de Supabase y ejecutar una sola vez.
-- =========================================================

create extension if not exists pgcrypto;

-- ---------------------------------------------------------
-- NUCLEO
-- ---------------------------------------------------------
create table if not exists sedes (
    id uuid primary key default gen_random_uuid(),
    nombre text not null unique,
    ubicacion text,
    created_at timestamptz not null default now()
);

create table if not exists usuarios (
    id uuid primary key default gen_random_uuid(),
    nombre text not null,
    matricula text not null unique,
    correo text,
    contrasena text not null,
    sede_id uuid references sedes(id),
    turno text check (turno in ('matutino', 'vespertino')),
    created_at timestamptz not null default now()
);

create table if not exists roles (
    id uuid primary key default gen_random_uuid(),
    nombre text not null unique
);

insert into roles (nombre)
select v from (values ('alumno'),('docente'),('comite'),('administrador'),('coordinacion')) as t(v)
where not exists (select 1 from roles where nombre = t.v);

create table if not exists usuario_roles (
    id uuid primary key default gen_random_uuid(),
    usuario_id uuid not null references usuarios(id) on delete cascade,
    rol_id uuid not null references roles(id) on delete cascade,
    unique (usuario_id, rol_id)
);

-- ---------------------------------------------------------
-- EVENTOS Y GOBERNANZA
-- ---------------------------------------------------------
create table if not exists eventos (
    id uuid primary key default gen_random_uuid(),
    titulo text not null,
    tipo text not null check (tipo in ('taller','curso','hackathon','buildathon','torneo')),
    descripcion text,
    sede_id uuid references sedes(id),
    ubicacion text,
    espacio text,
    responsable_id uuid references usuarios(id),
    colaboradores text,
    para_quienes text,
    fecha_hora_inicio timestamptz,
    fecha_hora_fin timestamptz,
    cupo int not null check (cupo >= 0),
    requisitos text,
    costo_presupuestado numeric(12,2) default 0,
    tiene_cuota boolean not null default false,
    cuota numeric(12,2) default 0,
    es_por_equipos boolean not null default false,
    modalidad_equipos text check (modalidad_equipos in ('libre','aleatoria')),
    tamano_equipo_max int,
    estado text not null default 'propuesta' check (estado in ('propuesta','aprobado','rechazado','publicado')),
    fecha_cierre_votacion timestamptz,
    bloqueado boolean not null default false,
    created_at timestamptz not null default now()
);

create table if not exists evento_sedes_permitidas (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    sede_id uuid not null references sedes(id),
    unique (evento_id, sede_id)
);

create table if not exists votos (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    usuario_id uuid not null references usuarios(id),
    sentido text not null check (sentido in ('a_favor','en_contra')),
    fecha timestamptz not null default now(),
    unique (evento_id, usuario_id)
);

create table if not exists comentarios (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    usuario_id uuid not null references usuarios(id),
    texto text not null,
    fecha timestamptz not null default now()
);

create table if not exists presupuesto_partidas (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    concepto text not null,
    monto numeric(12,2) not null
);

create table if not exists resoluciones_votacion (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    votos_a_favor int not null default 0,
    votos_en_contra int not null default 0,
    quorum_requerido int not null default 0,
    quorum_alcanzado boolean not null default false,
    resultado text not null check (resultado in ('aprobada','rechazada')),
    justificacion text not null,
    registrada_por uuid not null references usuarios(id),
    creado_en timestamptz not null default now()
);

-- ---------------------------------------------------------
-- INSCRIPCION Y OPERACION
-- ---------------------------------------------------------
create table if not exists inscripciones (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id),
    usuario_id uuid not null references usuarios(id),
    estado text not null default 'confirmada' check (estado in ('pendiente','confirmada','cancelada')),
    fecha timestamptz not null default now(),
    unique (evento_id, usuario_id)
);

create table if not exists pagos_simulados (
    id uuid primary key default gen_random_uuid(),
    inscripcion_id uuid not null unique references inscripciones(id) on delete cascade,
    estado text not null default 'pendiente' check (estado in ('pendiente','completado')),
    monto numeric(12,2),
    fecha timestamptz not null default now()
);

create table if not exists gafetes (
    id uuid primary key default gen_random_uuid(),
    inscripcion_id uuid not null unique references inscripciones(id) on delete cascade,
    folio text not null unique,
    tipo text not null check (tipo in ('alumno','tallerista')),
    fecha_generacion timestamptz not null default now()
);

create table if not exists asistencia (
    id uuid primary key default gen_random_uuid(),
    inscripcion_id uuid not null references inscripciones(id) on delete cascade,
    hora timestamptz not null default now(),
    registrado_por uuid references usuarios(id)
);

create table if not exists equipos (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid not null references eventos(id) on delete cascade,
    nombre text not null,
    tamano_max int
);

create table if not exists equipo_miembros (
    id uuid primary key default gen_random_uuid(),
    equipo_id uuid not null references equipos(id) on delete cascade,
    usuario_id uuid not null references usuarios(id),
    unique (equipo_id, usuario_id)
);

-- ---------------------------------------------------------
-- MODULOS SECUNDARIOS
-- ---------------------------------------------------------
create table if not exists foro_posts (
    id uuid primary key default gen_random_uuid(),
    evento_id uuid references eventos(id),
    usuario_id uuid not null references usuarios(id),
    titulo text not null,
    contenido text not null,
    fecha timestamptz not null default now()
);

create table if not exists foro_comentarios (
    id uuid primary key default gen_random_uuid(),
    post_id uuid not null references foro_posts(id) on delete cascade,
    usuario_id uuid not null references usuarios(id),
    contenido text not null,
    fecha timestamptz not null default now()
);

create table if not exists sugerencias (
    id uuid primary key default gen_random_uuid(),
    usuario_id uuid not null references usuarios(id),
    titulo text not null,
    descripcion text not null,
    estado text not null default 'nueva' check (estado in ('nueva','revisada','descartada')),
    fecha timestamptz not null default now()
);

-- ---------------------------------------------------------
-- PERMISOS (RLS desactivado en todo el prototipo, control vía backend PHP)
-- ---------------------------------------------------------
do $$
declare t text;
begin
  for t in select tablename from pg_tables where schemaname = 'public'
  loop
    execute format('alter table %I disable row level security;', t);
  end loop;
end $$;

grant usage on schema public to anon, authenticated;
grant select, insert, update, delete on all tables in schema public to anon, authenticated;
alter default privileges in schema public grant select, insert, update, delete on tables to anon, authenticated;

-- =========================================================
-- SEED: datos de prueba (ON CONFLICT DO NOTHING = seguro de re-correr)
-- =========================================================

insert into sedes (nombre) values
    ('Guamúchil'), ('Mocorito'), ('Culiacán'), ('Mazatlán')
on conflict (nombre) do nothing;

insert into usuarios (nombre, matricula, correo, contrasena, sede_id, turno) values
('Ana Torres', '24060101', 'ana.torres@alumno.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), 'matutino'),
('Luis Beltrán', '24060102', 'luis.beltran@alumno.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), 'vespertino'),
('Karla Núñez', '24060103', 'karla.nunez@alumno.mx', '1234567890', (select id from sedes where nombre = 'Culiacán'), 'matutino'),
('Jorge Ibarra', '24060104', 'jorge.ibarra@alumno.mx', '1234567890', (select id from sedes where nombre = 'Culiacán'), 'vespertino'),
('Diana Félix', '24060105', 'diana.felix@alumno.mx', '1234567890', (select id from sedes where nombre = 'Mocorito'), 'matutino'),
('Ricardo Payán', '23060106', 'ricardo.payan@alumno.mx', '1234567890', (select id from sedes where nombre = 'Mocorito'), 'vespertino'),
('Sofía Lizárraga', '23060107', 'sofia.lizarraga@alumno.mx', '1234567890', (select id from sedes where nombre = 'Mazatlán'), 'matutino'),
('Emilio Osuna', '23060108', 'emilio.osuna@alumno.mx', '1234567890', (select id from sedes where nombre = 'Mazatlán'), 'vespertino'),
('Paola Verdugo', '24060109', 'paola.verdugo@alumno.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), 'matutino'),
('Héctor Camacho', '24060110', 'hector.camacho@alumno.mx', '1234567890', (select id from sedes where nombre = 'Culiacán'), 'vespertino'),
('Prof. Manuel Rendón', '1000015', 'manuel.rendon@docente.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), null),
('Prof. Alicia Montoya', '1000016', 'alicia.montoya@docente.mx', '1234567890', (select id from sedes where nombre = 'Culiacán'), null),
('Prof. Iván Zazueta', '1000017', 'ivan.zazueta@docente.mx', '1234567890', (select id from sedes where nombre = 'Mocorito'), null),
('Coord. Brenda Salazar', '1000018', 'brenda.salazar@coordinacion.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), null),
('Admin. Fernando Lugo', '1000019', 'fernando.lugo@admin.mx', '1234567890', (select id from sedes where nombre = 'Guamúchil'), null)
on conflict (matricula) do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula in ('24060101','24060102','24060103','24060104','24060105','23060106','23060107','23060108','24060109','24060110')
  and r.nombre = 'alumno'
on conflict do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula in ('24060103','23060107') and r.nombre = 'comite'
on conflict do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula in ('1000015','1000016','1000017') and r.nombre = 'docente'
on conflict do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula = '1000018' and r.nombre = 'coordinacion'
on conflict do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula = '1000019' and r.nombre = 'administrador'
on conflict do nothing;

insert into usuario_roles (usuario_id, rol_id)
select u.id, r.id from usuarios u, roles r
where u.matricula in ('1000015','1000018') and r.nombre = 'comite'
on conflict do nothing;

-- Backfill: cualquier evento existente queda visible al menos para su sede organizadora
insert into evento_sedes_permitidas (evento_id, sede_id)
select id, sede_id from eventos
where not exists (select 1 from evento_sedes_permitidas esp where esp.evento_id = eventos.id);

-- =========================================================
-- Fin del script.
-- =========================================================