# EcoReto

Aplicación de retos ecológicos del Proyecto 05 de Aplicaciones de Internet 2026. Su objetivo es registrar acciones verificadas y mostrar el progreso de las personas y sus grupos mediante puntos.

## Características del sistema

- Catálogo con fechas y puntos, búsqueda por texto, filtro por categoría y detalle de cada reto.
- Vistas iniciales de ranking, formulario de participación y estado pendiente.
- En desarrollo: persistencia de participaciones, roles, moderación, CRUD de retos y ranking con estadísticas basadas en aprobaciones.

El avance utiliza datos de ejemplo. El formulario y la vista pendiente no guardan participaciones; los puntos del ranking son ilustrativos.

## Tecnologías utilizadas

HTML5, Tailwind CSS por CDN de desarrollo, Google Fonts, JavaScript nativo y PHP puro 8.x. La persistencia prevista utiliza SQLite con PDO. No requiere frameworks, Composer ni Node.js.

## Requisitos de ejecución

- PHP 8.x; versión probada: 8.5.12.
- Extensión PDO SQLite para la etapa de persistencia.
- Git y acceso al repositorio.
- Navegador moderno y conexión a internet para estilos y fuentes.
- Python 3, opcional para las pruebas HTTP.

## Instalación y ejecución local

```sh
git clone https://github.com/MarioDiazRamos/EcoReto.git
cd EcoReto
git switch feature/catalogo-navegacion
php -S 127.0.0.1:8000 -t public
```

Abrir http://127.0.0.1:8000/index.php. Detener el servidor con `Ctrl+C`.

El avance está disponible en `feature/catalogo-navegacion`, correspondiente al [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8). Tras su integración, se podrá ejecutar desde `develop` actualizada. No requiere variables de entorno, inicialización de base de datos ni cuentas de acceso en esta etapa.

## Organización del proyecto

| Ruta | Contenido |
|---|---|
| `public/` | Páginas y recursos accesibles desde el navegador |
| `src/` | Consultas al catálogo, datos de ejemplo y vistas compartidas |
| `storage/` | Ubicación prevista para SQLite; archivos locales excluidos de Git |
| `tests/` | Pruebas de lógica PHP y recorridos HTTP |
| `docs/` | Backlog, modelo, colaboración y entregas |

## Pruebas

Con el servidor iniciado, ejecutar en otra terminal desde la raíz del proyecto:

```sh
php tests/smoke.php
python tests/http_smoke.py
```

Resultados registrados: 14 pruebas PHP y 38 comprobaciones HTTP satisfactorias, con 16 rutas internas recorridas. Las pruebas HTTP no evalúan la presentación visual ni la interacción en navegador.

## Documentación y backlog

- [Backlog y calendario](docs/BACKLOG.md).
- [Historias y criterios de aceptación](https://github.com/MarioDiazRamos/EcoReto/issues).
- [Equipo y colaboración](docs/EQUIPO.md).
- [Modelo de datos y reglas de puntuación](docs/MODELO.md).
- [Entrega de la primera revisión y evidencias](docs/ENTREGA-01.md).
- [Bocetos de pantallas](docs/bocetos.svg).
