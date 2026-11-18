# Entrega 1 — EcoReto

Proyecto: 05 — EcoReto, Aplicaciones de Internet 2026.
Equipo: Mario Diaz Ramos (@MarioDiazRamos); responsabilidades adicionales por asignar.
Revisión: 1 — jueves 19 de noviembre de 2026, 25 puntos.
Repositorio: https://github.com/MarioDiazRamos/EcoReto
Rama de integración: `develop`.
Demostración: http://127.0.0.1:8000/index.php, con el servidor local iniciado.

## 1. Objetivo de la entrega

Presentar el catálogo y la navegación inicial, demostrar búsqueda y filtrado mediante PHP y establecer el modelo y backlog del sistema.

## 2. Funcionalidades

| Historia | Funcionalidad | Responsable | Rama | PR | Estado |
|---|---|---|---|---|---|
| HU-01 | Catálogo, búsqueda, filtros, detalle y navegación | MarioDiazRamos | feature/catalogo-navegacion | [#8](https://github.com/MarioDiazRamos/EcoReto/pull/8) | Implementada; revisión e integración pendientes |
| HU-05, vista inicial | Ranking ilustrativo por persona y grupo | MarioDiazRamos | feature/catalogo-navegacion | #8 | Vista disponible; cálculo persistente pendiente |

El formulario de participación y la vista pendiente son prototipos sin almacenamiento. La búsqueda PHP constituye la funcionalidad dinámica de esta entrega.

## 3. Instalación y ejecución

1. Disponer de PHP 8.x, Git, navegador e internet para Tailwind CSS y Google Fonts. Versión probada: PHP 8.5.12. PDO SQLite se utilizará en la siguiente etapa.
2. Clonar el repositorio con acceso autorizado y abrir el directorio `EcoReto`.
3. Seleccionar `feature/catalogo-navegacion` mientras el PR #8 esté abierto; después de integrarlo, utilizar develop actualizada.
4. No se requieren dependencias adicionales, variables de entorno, base de datos inicial ni cuentas de acceso en este avance.
5. Ejecutar `php -S 127.0.0.1:8000 -t public`.
6. Abrir http://127.0.0.1:8000/index.php. Detener el servidor con `Ctrl+C`.

Comandos completos en [README](../README.md).

## 4. Modelo de datos

[Entidades, relaciones y reglas](MODELO.md). El catálogo de ejemplo se encuentra en `src/retos.php`. La persistencia de participaciones, validaciones y puntos está pendiente. [Bocetos de pantallas](bocetos.svg).

## 5. Evidencias de pruebas

Pruebas ejecutadas el 17/11/2026:

```sh
php tests/smoke.php
python tests/http_smoke.py
```

La prueba HTTP requiere el servidor iniciado; Python solo se utiliza para esa prueba. Resultados: **14 pruebas PHP y 38 comprobaciones HTTP satisfactorias**, con **16 rutas internas** recorridas. Los nueve archivos PHP pasan la revisión de sintaxis; `git diff --check` no presenta errores.

| ID | Acción | Resultado esperado | Resultado obtenido | Evidencia | Estado |
|---|---|---|---|---|---|
| T-01 | Consultar /index.php | Mostrar seis retos con fechas y puntos | HTTP 200; seis tarjetas | tests/http_smoke.py | Correcto |
| T-02 | Buscar q=agua | Coincidir en la descripción | HTTP 200; una tarjeta | Ambas suites | Correcto |
| T-03 | Buscar ENERGIA y consultar category=Residuos por separado | Normalizar acentos y filtrar categoría | Una y dos coincidencias, respectivamente | Ambas suites | Correcto |
| T-04 | Combinar q=agua y category=Residuos | Mostrar estado vacío | HTTP 200; cero tarjetas y mensaje | tests/http_smoke.py | Correcto |
| T-05 | Enviar array, consulta excesiva, UTF-8 inválido o categoría desconocida | Rechazar parámetros inválidos | HTTP 400 | Ambas suites | Correcto, negativo |
| T-06 | Introducir una etiqueta script en q | Escapar HTML | Salida escapada, sin etiqueta script cruda | Ambas suites | Correcto, negativo |
| T-07 | Consultar id=-1 e id=999 | Distinguir entrada inválida y reto inexistente | HTTP 400 y 404 con recuperación | Ambas suites | Correcto, negativo |
| T-08 | Recorrer enlaces de catálogo, detalles y vistas iniciales | Evitar enlaces internos rotos | 16 rutas con HTTP 200 | tests/http_smoke.py | Correcto |
| T-09 | Solicitar /src/retos.php y /storage/.gitkeep | Impedir acceso fuera de public | HTTP 404 | tests/http_smoke.py | Correcto |
| T-10 | Abrir formulario, pendiente y ranking | Mostrar contexto y datos iniciales | Reto seleccionado, aviso de prototipo, cero puntos pendientes y ranking ilustrativo | tests/http_smoke.py | Correcto por HTTP |
| T-11 | Revisar presentación móvil y escritorio, menú y previsualización | Comprobar distribución e interacción | Por ejecutar en navegador | Revisión visual | Pendiente |

Tailwind CSS y Google Fonts respondieron HTTP 200 con contenido no vacío. No se detectaron patrones de credenciales ni bases SQLite en los archivos versionados; sus exclusiones se comprobaron mediante gitignore.

## 6. Enlaces GitHub

- [Historias e Issues](https://github.com/MarioDiazRamos/EcoReto/issues) y [backlog](BACKLOG.md).
- [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8) y [commits](https://github.com/MarioDiazRamos/EcoReto/pull/8/commits).
- Ramas: [master](https://github.com/MarioDiazRamos/EcoReto/tree/master), [develop](https://github.com/MarioDiazRamos/EcoReto/tree/develop) y [feature/catalogo-navegacion](https://github.com/MarioDiazRamos/EcoReto/tree/feature/catalogo-navegacion).

## 7. Pendientes

Revisión visual de la interfaz y revisión e integración del PR #8. Participaciones persistentes, autenticación, moderación, CRUD de retos, asignación de puntos y ranking real corresponden a las siguientes historias del backlog. La release final está pendiente.

## 8. Participación

Mario Diaz Ramos (@MarioDiazRamos): catálogo, búsqueda, filtros, detalle, vistas iniciales y documentación de la entrega. Evidencia: commits del PR #8. Las funcionalidades restantes están por asignar; consultar [Equipo y colaboración](EQUIPO.md).

## 9. Demostración

1. Mostrar el catálogo y sus fechas y puntos.
2. Buscar agua; combinar con Residuos para presentar el estado vacío.
3. Abrir un detalle y recorrer las vistas de ranking, formulario y pendiente.
4. Ejecutar las pruebas y presentar modelo, historias y PR.

La demostración final incorporará el recorrido persistente completo y la explicación de la funcionalidad desarrollada por cada integrante, con duración de 8–10 minutos.
