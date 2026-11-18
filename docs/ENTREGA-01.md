# Entrega 1 — EcoReto

Formato del PDF original, pp. 32–33. Evidencia ejecutada el 17/11/2026; no sustituye revisión del equipo.

Proyecto: 05 — EcoReto. Equipo: MarioDiazRamos (Mario Diaz Ramos); otros integrantes pendientes de identificar.
Revisión / fecha: 1 — jueves 19/11/2026 (25 puntos).
Repositorio: https://github.com/MarioDiazRamos/EcoReto (privado; docente necesita acceso).
Rama de integración: develop. Demo local: http://127.0.0.1:8000 mientras se ejecute el servidor; no hay despliegue público.

## 1. Objetivo de esta entrega

Consultar y buscar retos reales de un conjunto semilla mediante PHP, conectar la navegación inicial y distribuir el trabajo futuro. No implementar todo el MVP.

## 2. Funcionalidades implementadas

| Historia | Qué hace | Responsable | Rama feature | PR | Estado |
|---|---|---|---|---|---|
| [HU-01](https://github.com/MarioDiazRamos/EcoReto/issues/1) | Catálogo, consulta GET por palabra/categoría, detalle y navegación inicial | MarioDiazRamos, con asistencia de código | feature/catalogo-navegacion | [#8](https://github.com/MarioDiazRamos/EcoReto/pull/8) | Implementada y probada; revisión/integración pendiente |
| HU-05, parte visual de revisión 1 | Ranking con datos sintéticos; no es cálculo real | Misma autoría del avance | Misma feature de navegación | #8 | Solo vista inicial; tarea de ranking real sigue abierta |

El formulario y la vista pendiente son prototipos rotulados: no envían ni guardan evidencia. La búsqueda PHP, no estas maquetas, demuestra la primera funcionalidad real.

## 3. Instrucciones para ejecutar

1. Requisitos: PHP puro 8.x (probado en 8.5.12); PDO SQLite disponible para la siguiente etapa. Navegador moderno e internet para Tailwind/Google Fonts. Python es opcional para pruebas HTTP.
2. Clonar `https://github.com/MarioDiazRamos/EcoReto.git` con acceso a este repositorio privado.
3. `cd EcoReto` y `git switch feature/catalogo-navegacion` mientras el PR esté abierto. Tras integrarlo, usar develop actualizada.
4. No hay dependencias de Composer/Node ni variables secretas. No se inicializa SQLite todavía; las semillas están en src/retos.php.
5. `php -S 127.0.0.1:8000 -t public`.
6. Abrir http://127.0.0.1:8000/index.php. No existen cuentas de demostración ni autenticación implementada. Detener con Ctrl+C.

## 4. Modelo de datos y cambios

[Modelo preliminar y reglas de puntos](MODELO.md). Semillas de retos fuera de public; futura base local en storage, excluida por gitignore. No hay esquema SQL persistente todavía. [Bocetos SVG](bocetos.svg) son planificación, no capturas de ejecución.

## 5. Evidencia de pruebas

Comandos ejecutados: `php tests/smoke.php` y, con servidor iniciado, `python tests/http_smoke.py`. Resultados: **14 pruebas PHP y 38 comprobaciones HTTP correctas**, 16 rutas internas recorridas. Sintaxis: los nueve archivos PHP, incluida la prueba CLI, pasaron php -l. git diff --check pasó; no se detectaron patrones de credenciales ni bases SQLite versionadas. Se comprobó que siete rutas de configuración, bases y caché quedan excluidas por gitignore. CDN Tailwind y Google Fonts respondieron HTTP 200 con contenido no vacío, lo cual no demuestra su renderizado en navegador.

| ID | Acción | Esperado | Obtenido | Evidencia | Estado |
|---|---|---|---|---|---|
| T-01 | GET /index.php | Seis retos, fechas y puntos | HTTP 200; seis tarjetas | tests/http_smoke.py | Correcto |
| T-02 | GET /index.php?q=agua | Coincidencia por descripción | HTTP 200; una tarjeta, Cada gota cuenta | Ambas suites | Correcto |
| T-03 | Buscar ENERGIA y filtrar Residuos por separado | Normalizar acentos; dos retos de Residuos | Una coincidencia para ENERGIA; dos para categoría | Ambas suites | Correcto |
| T-04 | q=agua y category=Residuos | Estado vacío sin errores | HTTP 200, cero tarjetas y mensaje | tests/http_smoke.py | Correcto |
| T-05 | q[]=agua, consulta >160 bytes, UTF-8 inválido o categoría desconocida | Rechazar parámetros inválidos | HTTP 400 | Ambas suites | Correcto, negativo |
| T-06 | q con etiqueta script | No ejecutar/incrustar HTML | Texto escapado, etiqueta cruda ausente | Ambas suites | Correcto, negativo |
| T-07 | Detalle id=-1 e id=999 | 400 inválido; 404 inexistente | Ambos estados y enlace de recuperación | Ambas suites | Correcto, negativo |
| T-08 | Recorrer enlaces de catálogo, detalles y vistas iniciales | Sin enlaces internos rotos | 16 rutas internas HTTP 200 | tests/http_smoke.py | Correcto |
| T-09 | Pedir /src/retos.php y /storage/.gitkeep | No exponer archivos privados | HTTP 404 | tests/http_smoke.py | Correcto |
| T-10 | Formulario contextual, pendiente y ranking | Vistas de ejemplo sin falso guardado | Reto seleccionado; aviso de no guardado; 0 puntos; ranking sintético | tests/http_smoke.py | Correcto por HTTP |
| T-11 | Navegador móvil y escritorio | Sin desbordes; menú y previsualización operativos | Sin motor de navegador disponible | Revisión manual por equipo | Pendiente |

Incidencias corregidas durante pruebas: el nombre inicial del test Python colisionaba con la biblioteca http (renombrado a http_smoke.py); el servidor PHP respondía catálogo 200 a rutas desconocidas (ahora índice comprueba la ruta y devuelve 404). No son pruebas de moderación ni de puntos persistentes, que aún no existen.

## 6. GitHub: enlaces

- [master](https://github.com/MarioDiazRamos/EcoReto/tree/master), [develop](https://github.com/MarioDiazRamos/EcoReto/tree/develop), [feature](https://github.com/MarioDiazRamos/EcoReto/tree/feature/catalogo-navegacion).
- Base común de master/develop: [1c660f1](https://github.com/MarioDiazRamos/EcoReto/commit/1c660f1). La feature se creó después de `git switch develop` y `git pull --ff-only origin develop`; merge-base coincide con esa base.
- [Consulta PHP, 66a4290](https://github.com/MarioDiazRamos/EcoReto/commit/66a4290); [interfaz y navegación, 61c34ba](https://github.com/MarioDiazRamos/EcoReto/commit/61c34ba). El commit posterior de documentación se consulta en [commits del PR](https://github.com/MarioDiazRamos/EcoReto/pull/8/commits).
- [PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8), origen feature/catalogo-navegacion, destino develop: **abierto, sin aprobación ni integración**.
- [Issues #1–#7](https://github.com/MarioDiazRamos/EcoReto/issues), criterios y dependencias en [backlog](BACKLOG.md). No hay release.

## 7. Problemas conocidos, pendientes y riesgos

Solo el propietario aparece como colaborador. Falta invitar integrantes reales y docente, comprobar visualmente la interfaz y obtener revisión e integración del primer PR antes del 19/11. Las protecciones de master y develop están habilitadas: requieren un PR con una aprobación, impiden force push y borrado; no se aplican de forma obligatoria a administradores. No se usará esa excepción para simular integración revisada.

Persistencia, roles, CRUD, moderación, cálculo de puntos y ranking/estadísticas reales están pendientes en HU-02 a HU-06. No se creó release. Las fechas del catálogo son ejemplos fijos de noviembre/diciembre de 2026. El CDN es solo de desarrollo.

## 8. Participación por integrante

La cuenta real MarioDiazRamos es la autora Git del arranque y esta feature, realizada con asistencia de código. No hay commits o revisiones atribuidos a otros. [Equipo, propuesta de reparto y cómo incorporarlos](EQUIPO.md). Los módulos futuros quedan sin asignación definitiva hasta conocer integrantes.

## 9. Guion de demostración

Mostrar seis retos; buscar agua; combinar con Residuos y explicar estado vacío; abrir detalle; recorrer ranking, formulario y ejemplo pendiente aclarando límites. Mostrar Issues, modelo y pruebas; explicar origen de feature, commits y estado del PR. La demo final será de 8–10 minutos y requerirá flujo persistente completo y módulo desarrollado por cada integrante.

## Estado frente a revisión 1

Disponibles: repositorio, master/develop/feature, README, siete historias e Issues, backlog, bocetos, modelo, reglas escritas, cinco vistas y navegación con marcado responsivo, búsqueda PHP real y pruebas automatizadas. Pendientes: revisión visual en navegador, integrantes/acceso docente, revisión real y primer PR integrado. No se declara completada toda la revisión 1.
