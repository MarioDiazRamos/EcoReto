# EcoReto — Primera revisión

**Proyecto:** 05 — EcoReto · Aplicaciones de Internet 2026  
**Fecha:** 19 de noviembre de 2026  
**Equipo:** Francisco Javier Zavala Martinez; Juan Rodolfo Mozqueda Lozano; Karlo Silvestre De la cruz Flores; Mario Diaz Ramos  
**Repositorio:** https://github.com/MarioDiazRamos/EcoReto  
**Rama de integración:** `develop`  
**Demostración local:** `http://127.0.0.1:8000/index.php`

## Objetivo y alcance

Mostrar el catálogo y las pantallas iniciales de EcoReto, demostrar la búsqueda y el filtrado de retos en PHP y documentar las historias de usuario, el modelo de datos y las reglas de puntuación.

## Funcionalidades presentadas

| Historia | Funcionalidad | Responsable | Evidencia | Estado |
|---|---|---|---|---|
| HU-01 | Catálogo de seis retos, búsqueda, filtros, detalle y navegación | Mario Diaz Ramos | [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8) | Integrada en `develop` |
| HU-05 (vista inicial) | Ranking ilustrativo por persona y grupo | Mario Diaz Ramos | [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8) | Vista inicial integrada |

El formulario de participación y la pantalla de estado pendiente son prototipos. No almacenan participaciones. El ranking muestra datos de ejemplo; no se asignan puntos reales en esta versión.

## Ejecución

Requisitos: Git, PHP 8.x y navegador con conexión a internet para los estilos y las fuentes.

```sh
git clone https://github.com/MarioDiazRamos/EcoReto.git
cd EcoReto
git switch develop
php -S 127.0.0.1:8000 -t public
```

Abrir `http://127.0.0.1:8000/index.php`. El proyecto puede iniciarse en esta etapa sin base de datos. [Instrucciones generales](../README.md).

## Datos y puntuación

El [modelo preliminar](MODELO.md) contempla retos, participantes, participaciones, validaciones y puntos. Las reglas establecen que una participación pendiente o rechazada no suma puntos y que solo una aprobación válida puede otorgarlos, sin duplicaciones.

## Evidencias de pruebas

**Versión evaluada:** `develop`, commit [`8c8374b`](https://github.com/MarioDiazRamos/EcoReto/commit/8c8374b9fce60c0aff1295be031f79cfe3c4f0cc).  
**Fecha:** 18 de noviembre de 2026.  
**Resultados:** 14 pruebas PHP y 38 comprobaciones HTTP correctas; 16 rutas internas recorridas.

```sh
php tests/smoke.php
python tests/http_smoke.py
```

| ID | Acción | Resultado esperado | Resultado obtenido | Evidencia | Estado |
|---|---|---|---|---|---|
| T-01 | Abrir el catálogo | Mostrar seis retos | HTTP 200 y seis tarjetas | `tests/http_smoke.py`; captura 01 | Correcto |
| T-02 | Buscar `agua` | Una coincidencia | HTTP 200 y un reto | `tests/http_smoke.py`; captura 02 | Correcto |
| T-03 | Filtrar categoría `Residuos` | Dos coincidencias | Dos retos | `tests/http_smoke.py` | Correcto |
| T-04 | Combinar `agua` con `Residuos` | Estado sin coincidencias | Cero retos y mensaje | `tests/http_smoke.py` | Correcto |
| T-05 | Enviar parámetros inválidos | HTTP 400 | HTTP 400 | `tests/smoke.php`; `tests/http_smoke.py` | Correcto, negativo |
| T-06 | Enviar una etiqueta HTML en la búsqueda | Escapar la salida | Etiqueta escapada | `tests/http_smoke.py` | Correcto, negativo |
| T-07 | Abrir reto inexistente | HTTP 404 | HTTP 404 | `tests/http_smoke.py` | Correcto, negativo |
| T-08 | Solicitar `/src/retos.php` | HTTP 404 | HTTP 404 | `tests/http_smoke.py` | Correcto, negativo |
| T-09 | Abrir detalle, ranking y formulario | Mostrar vistas iniciales | HTTP 200 | `tests/http_smoke.py`; capturas 03–05 | Correcto |

## Capturas de pantalla

| Captura | Pantalla |
|---|---|
| [01_catalogo.png](evidencias/revision-01/01_catalogo.png) | Catálogo y filtros |
| [02_busqueda_agua.png](evidencias/revision-01/02_busqueda_agua.png) | Búsqueda por `agua` |
| [03_detalle_reto.png](evidencias/revision-01/03_detalle_reto.png) | Detalle del reto |
| [04_ranking.png](evidencias/revision-01/04_ranking.png) | Ranking ilustrativo |
| [05_formulario_participacion.png](evidencias/revision-01/05_formulario_participacion.png) | Formulario de participación |

## Evidencia de GitHub

- [Historias de usuario](https://github.com/MarioDiazRamos/EcoReto/issues) y [backlog](BACKLOG.md)
- [PR #8 revisado e integrado](https://github.com/MarioDiazRamos/EcoReto/pull/8)
- [Commit de integración](https://github.com/MarioDiazRamos/EcoReto/commit/8c8374b9fce60c0aff1295be031f79cfe3c4f0cc)
- [Rama develop](https://github.com/MarioDiazRamos/EcoReto/tree/develop) y [rama master](https://github.com/MarioDiazRamos/EcoReto/tree/master)

## Pendientes

La persistencia, la moderación, la administración de retos y la puntuación real corresponden a la segunda revisión. El ranking con datos reales corresponde a la entrega final.

## Participación

Las responsabilidades de los cuatro integrantes se encuentran en [Equipo y responsabilidades](EQUIPO.md). En esta revisión, Mario Diaz Ramos desarrolló la funcionalidad presentada en el [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8), revisado y aprobado por Karlo Silvestre De la cruz Flores.

## Demostración

1. Consultar el catálogo con fechas y puntos.
2. Buscar `agua` y aplicar filtros.
3. Abrir un reto, mostrar el formulario, el estado pendiente y el ranking ilustrativo.
4. Presentar el modelo de datos, los resultados de pruebas y el PR #8 integrado.
