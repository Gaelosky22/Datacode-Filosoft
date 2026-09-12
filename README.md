# Data Code — Portal de eventos y talleres UAdeO

Portal oficial para proponer, votar, publicar e inscribirse a talleres y eventos
de la Unidad Regional Guamúchil de la UAdeO. Prototipo desarrollado para el
Buildathon DATA CODE 1.0, como base funcional para DATA CODE 2.0 (2027).

## Descripción

El sistema permite:

- Consultar eventos y talleres publicados desde una pantalla pública de inicio.
- Registro e inicio de sesión simulado por matrícula/contraseña (sin verificación
  de correo, según alcance del prototipo).
- **Portal de alumnos:** inscripción a eventos de su sede, simulación de pago
  para eventos con cuota, y generación de gafete digital imprimible.
- **Portal de comité:** propuesta de talleres/eventos por docentes, votación y
  discusión por parte del comité (restringida a las sedes con permiso sobre
  cada propuesta), y resolución final (aprobar/rechazar) por el administrador.
- **Panel de administración:** gestión de qué usuarios pertenecen al comité,
  filtrable por sede y búsqueda por matrícula o nombre.

## Stack técnico

| Capa | Tecnología |
|---|---|
| Backend | PHP nativo (arquitectura MVC, sin framework) |
| Interacciones dinámicas | HTMX (vía CDN) |
| Estilos | CSS propio (sin librería de componentes) |
| Tipografía | Google Fonts — Fraunces (vía CDN) |
| Base de datos | PostgreSQL en Supabase, consumida vía API REST (PostgREST) |
| Autenticación | Sesiones nativas de PHP + tabla `usuarios` propia (sin Supabase Auth) |

No se usa Composer ni dependencias instaladas localmente — todo el frontend se
carga por CDN y el backend es PHP puro con `curl` para hablar con Supabase.

## Requisitos previos

- **PHP 8.1 o superior**, con la extensión **`curl`** habilitada (revisar con
  `php -m | findstr curl` en Windows o `php -m | grep curl` en Linux/Mac).
- En **Windows**, además se necesita un archivo de certificados raíz para que
  `curl` valide SSL correctamente (ver sección de Configuración).
- Una cuenta de **Supabase** con un proyecto creado, con el esquema de base de
  datos ya ejecutado (ver `docs/` y los scripts SQL del repositorio).
- No se requiere servidor web adicional para desarrollo local — se usa el
  servidor embebido de PHP.

## Instalación

1. Clona este repositorio:
   ```
   git clone <url-del-repositorio>
   cd datacode
   ```
2. No hay dependencias que instalar vía gestor de paquetes — el proyecto no
   usa Composer ni npm.

## Configuración

El proyecto **no incluye credenciales en el código** — todo se configura por
variables de entorno en un archivo `.env` local (nunca se sube al repositorio).

1. Copia el archivo de ejemplo:
   ```
   # Windows (PowerShell)
   Copy-Item .env.example .env

   # Linux/Mac
   cp .env.example .env
   ```
2. Abre `.env` y completa los valores con los datos de tu proyecto de Supabase
   (Project Settings → API Keys y → Connection):
   ```
   SUPABASE_URL=https://tu-proyecto.supabase.co
   SUPABASE_ANON_KEY=tu_publishable_key
   ```
   Usa la llave **`anon` / `publishable`**, nunca la `service_role`/secret —
   la anon key está diseñada para exponerse en el cliente y es la única que
   necesita este proyecto, ya que el control de permisos se hace en el backend
   de PHP, no mediante Row Level Security en Supabase (RLS queda desactivado
   intencionalmente en todas las tablas para este prototipo).
3. Ejecuta contra tu proyecto de Supabase, en este orden, los scripts SQL de
   la carpeta del repositorio: el esquema (`schema_datacode.sql`), los grants
   de permisos, y opcionalmente el seed de datos de prueba
   (`seed_datacode.sql`) para tener usuarios y eventos de ejemplo.
4. **Solo en Windows**, si al ejecutar ves un error de
   `SSL certificate problem: unable to get local issuer certificate`:
   - Descarga `https://curl.se/ca/cacert.pem`.
   - En tu `php.ini`, agrega (sin punto y coma al inicio):
     ```
     curl.cainfo = "C:/ruta/donde/lo/guardaste/cacert.pem"
     ```
   - Reinicia el servidor de PHP después de guardar el `.ini`.

## Ejecución

Desde la **raíz del proyecto** (no dentro de `public/`):

```
php -S localhost:8000 -t public
```

Abre `http://localhost:8000/` en el navegador.

## Credenciales de demostración

Todas las contraseñas de las cuentas de prueba son: **`1234567890`**

| Rol | Matrícula | Notas |
|---|---|---|
| Alumno | `24060103` | También pertenece al comité (sede Culiacán) |
| Alumno | `23060107` | También pertenece al comité (sede Mazatlán) |
| Alumno | `24060101` | Solo alumno, sede Guamúchil |
| Docente | `1000015` | También pertenece al comité |
| Docente | `1000016` | Solo docente |
| Coordinación | `1000018` | También pertenece al comité |
| Administrador | `1000019` | Acceso total: resolución de propuestas y gestión de comité |

> Estas cuentas y contraseñas son datos ficticios generados exclusivamente
> para la demostración del prototipo — no representan personas reales ni
> credenciales sensibles.

## Estructura del proyecto

```
datacode/
├── public/            → document root (único directorio expuesto al servidor web)
├── core/              → Router, Auth, SupabaseClient
├── controllers/       → controladores por sección (Inicio, Auth, Alumno, Comite, Admin)
├── models/            → acceso a datos vía SupabaseClient
├── views/             → vistas PHP, organizadas por sección
├── config/            → configuración (lee variables de entorno, sin secretos)
├── docs/              → bases oficiales, bitácora de planeación y alcance final
├── .env.example       → plantilla de variables de entorno
└── README.md
```

Ver `docs/03_alcance_final_v2.md` para el detalle completo de reglas de
negocio, prioridades de desarrollo y criterios de aceptación del prototipo.

## Licencia

Este proyecto se distribuye bajo licencia MIT — ver `LICENSE.md`.

### Créditos de terceros

- [HTMX](https://htmx.org/) — licencia BSD 2-Clause.
- [Google Fonts — Fraunces](https://fonts.google.com/specimen/Fraunces) — licencia SIL Open Font License.
- [Supabase](https://supabase.com/) — plataforma de base de datos utilizada como backend.
