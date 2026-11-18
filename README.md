# EcoReto

Proyecto 05 de Aplicaciones de Internet 2026. Las campañas ecológicas suelen registrar manualmente la participación y ofrecen poca información sobre el avance. EcoReto permite consultar retos con fechas y puntos; el MVP completo contempla registrar participaciones, validar evidencias, asignar puntos tras su aprobación y visualizar resultados por persona y grupo.

## Funcionalidades

- Catálogo de seis retos iniciales, con búsqueda por texto, filtro por categoría y detalle.
- Vistas de participación, estado pendiente y ranking con datos de ejemplo.
- En desarrollo para las siguientes revisiones: persistencia SQLite, autenticación, moderación, administración de retos, puntuación y estadísticas reales.

En esta primera versión, el formulario no registra participaciones y el ranking muestra datos ilustrativos. Quedan fuera del alcance las recompensas monetarias y la verificación automática de residuos mediante fotografías.

## Tecnologías

HTML5, Tailwind CSS, Google Fonts, JavaScript nativo y PHP 8.x. La persistencia prevista utiliza SQLite mediante PDO.

## Ejecución local

Requisitos: Git, PHP 8.x y navegador con conexión a internet para cargar estilos y fuentes.

```sh
git clone https://github.com/MarioDiazRamos/EcoReto.git
cd EcoReto
git switch develop
php -S 127.0.0.1:8000 -t public
```

Abrir `http://127.0.0.1:8000/index.php`. Detener el servidor con `Ctrl+C`.

## Pruebas

Desde la raíz del proyecto, con el servidor iniciado:

```sh
php tests/smoke.php
python tests/http_smoke.py
```

Resultados registrados el 18 de noviembre de 2026 sobre `develop`: 14 pruebas PHP y 38 comprobaciones HTTP correctas; 16 rutas internas recorridas. Python 3 se utiliza únicamente para las pruebas HTTP.

## Documentación

- [Historias de usuario](https://github.com/MarioDiazRamos/EcoReto/issues)
- [Backlog y calendario](docs/BACKLOG.md)
- [Integrantes y responsabilidades](docs/EQUIPO.md)
- [Modelo de datos y reglas de puntuación](docs/MODELO.md)
- [Primera entrega y evidencias](docs/ENTREGA-01.md)
