# Backlog de EcoReto

Los Issues contienen las historias de usuario y sus criterios de aceptación. Este backlog resume su planificación, dependencias y responsables.

| Historia | Funcionalidad | Dependencias | Revisión | Responsable | Estado |
|---|---|---|---|---|---|
| [HU-01 · #1](https://github.com/MarioDiazRamos/EcoReto/issues/1) | Catálogo, búsqueda, detalle y navegación inicial | Ninguna | 1 | MarioDiazRamos | Implementada; PR #8 pendiente de revisión e integración |
| [HU-02 · #2](https://github.com/MarioDiazRamos/EcoReto/issues/2) | Registro persistente y consulta del estado de participaciones | HU-01; contrato de identidad de HU-03 | 2 | Por asignar | Pendiente |
| [HU-03 · #3](https://github.com/MarioDiazRamos/EcoReto/issues/3) | Autenticación, roles y moderación | HU-02; coordinación con HU-06 | 2 | Por asignar | Pendiente |
| [HU-04 · #4](https://github.com/MarioDiazRamos/EcoReto/issues/4) | CRUD de retos | HU-01 y autorización de HU-03 | 2 | Por asignar | Pendiente |
| [HU-05 · #5](https://github.com/MarioDiazRamos/EcoReto/issues/5) | Ranking y estadísticas por persona o grupo | HU-06 para puntuación real | 1: vista inicial; 3: datos reales | Por asignar | Vista inicial disponible; cálculo real pendiente |
| [HU-06 · #6](https://github.com/MarioDiazRamos/EcoReto/issues/6) | Asignación atómica e idempotente de puntos | HU-02 y HU-03 | 2 y 3 | Por asignar | Pendiente |
| [HU-07 · #7](https://github.com/MarioDiazRamos/EcoReto/issues/7) | Instalación reproducible, pruebas y entrega | Funcionalidades de cada revisión | 1, 2 y 3 | Por asignar | En curso |

## Calendario de entregas

| Revisión | Fecha | Valor | Entregables principales |
|---|---|---|---|
| 1 | 19/11/2026 | 25 puntos | Vistas iniciales, función real, documentación y primer PR revisado e integrado en develop |
| 2 | 26/11/2026 | 35 puntos | CRUD, persistencia, moderación, puntos, cinco pruebas —dos negativas— y dos PR adicionales integrados |
| Final | 03/12/2026 | 40 puntos | Ranking y progreso reales, ocho pruebas —tres negativas—, instalación reproducible y release 1.0.0 |

Las responsabilidades y el procedimiento de integración se describen en [Equipo y colaboración](EQUIPO.md). El estado de la primera revisión se registra en [Entrega 1](ENTREGA-01.md).
