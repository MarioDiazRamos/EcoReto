# Historias y backlog

Criterios completos versionados aquí; los Issues sirven para asignación y seguimiento. Estado: HU-01 implementada en la feature y pendiente de revisión/integración; demás funcionalidades pendientes. HU-05 tiene únicamente vista inicial sintética. No hay revisores externos registrados.

| Historia | Issue | Revisión | Dependencias | Responsable |
|---|---|---|---|---|
| HU-01: Consultar y buscar retos con navegación inicial | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/1) | 1 — 19/11 | Ninguna | MarioDiazRamos |
| HU-02: Guardar participaciones y consultar su estado | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/2) | 2 — 26/11 | HU-01; contrato de identidad de HU-03 | Sin asignar |
| HU-03: Autenticar roles y moderar evidencias | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/3) | 2 — 26/11 | HU-02; coordinar HU-06 | Sin asignar |
| HU-04: Administrar retos con CRUD | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/4) | 2 — 26/11 | HU-01 y autorización HU-03 | Sin asignar |
| HU-05: Consultar ranking y estadísticas reales | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/5) | 1: vista inicial; 3 — 03/12: datos reales | HU-06 para puntuación real | Sin asignar |
| HU-06: Otorgar puntos una sola vez por aprobación | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/6) | 2 — 26/11; robustez final 03/12 | HU-02 y HU-03 | Sin asignar |
| HU-07: Reproducir instalación, pruebas y entrega final | [Issue](https://github.com/MarioDiazRamos/EcoReto/issues/7) | 1, 2 y 3 | HU-01 a HU-06 según revisión | Sin asignar |

## HU-01 — Consultar y buscar retos con navegación inicial

Como participante, quiero consultar catálogo y detalle para elegir un reto.

Revisión: 1 — 19/11. Dependencias: Ninguna. Responsable: MarioDiazRamos.

- [x] El catálogo muestra título, fechas y puntos de cada semilla.
- [x] GET q busca por título o descripción y category filtra; ambos criterios se combinan.
- [x] Una búsqueda sin coincidencias muestra estado vacío; parámetros inválidos devuelven 400 y reto inexistente 404.
- [x] Detalle y navegación permiten volver al catálogo; no se ejecuta HTML enviado en la búsqueda.
- [x] Las vistas iniciales de ranking, formulario y pendiente se identifican como ejemplos sin persistencia.

## HU-02 — Guardar participaciones y consultar su estado

Como participante, quiero registrar evidencia textual y recuperar su estado después de actualizar.

Revisión: 2 — 26/11. Dependencias: HU-01; contrato de identidad de HU-03. Responsable: Sin asignar.

- [ ] Un POST válido guarda en SQLite fuera de public y comienza pendiente.
- [ ] Evidencia vacía, reto inválido o fecha no permitida se rechazan en servidor.
- [ ] Actualizar o reiniciar el servidor conserva el registro; pendiente suma cero.
- [ ] Solo el autor o un moderador autorizado accede a la evidencia; POST exige CSRF.

## HU-03 — Autenticar roles y moderar evidencias

Como moderador, quiero aprobar o rechazar evidencia con permisos comprobados.

Revisión: 2 — 26/11. Dependencias: HU-02; coordinar HU-06. Responsable: Sin asignar.

- [ ] Sesiones con password_hash/password_verify distinguen participante y moderador.
- [ ] Un participante no puede validar aunque manipule el POST.
- [ ] Un moderador no puede validar su propia evidencia.
- [ ] Aprobar/rechazar registra decisión y moderador; error de CSRF impide cambios.

## HU-04 — Administrar retos con CRUD

Como moderador, quiero crear, consultar, editar y retirar retos.

Revisión: 2 — 26/11. Dependencias: HU-01 y autorización HU-03. Responsable: Sin asignar.

- [ ] Crear y editar datos válidos persiste cambios al recargar.
- [ ] Puntos no positivos y fechas invertidas producen errores claros.
- [ ] Solo moderadores pueden escribir; cada cambio exige POST y CSRF.
- [ ] Retirar un reto conserva la integridad de participaciones existentes; política se acuerda antes de implementar.

## HU-05 — Consultar ranking y estadísticas reales

Como participante, quiero ver progreso individual y por grupo basado en aprobaciones.

Revisión: 1: vista inicial; 3 — 03/12: datos reales. Dependencias: HU-06 para puntuación real. Responsable: Sin asignar.

- [ ] La primera revisión muestra datos sintéticos rotulados sin atribuirlos a usuarios reales.
- [ ] En versión final el ranking cambia al aprobar una participación y se conserva al recargar.
- [ ] Se puede consultar por persona o grupo; los totales coinciden con asientos aprobados.
- [ ] Sin aprobaciones se muestra cero o estado vacío; pendientes y rechazos no cuentan.

## HU-06 — Otorgar puntos una sola vez por aprobación

Como moderador, quiero asignar puntos de forma atómica y verificable.

Revisión: 2 — 26/11; robustez final 03/12. Dependencias: HU-02 y HU-03. Responsable: Sin asignar.

- [ ] Pendiente y rechazada producen cero asientos de puntos.
- [ ] Aprobar guarda exactamente el valor autorizado por el servidor.
- [ ] Aprobar dos veces o repetir solicitudes no cambia el total después de la primera aprobación.
- [ ] Fallo al guardar puntos revierte también la aprobación.

## HU-07 — Reproducir instalación, pruebas y entrega final

Como equipo, quiero demostrar el sistema y las contribuciones reales.

Revisión: 1, 2 y 3. Dependencias: HU-01 a HU-06 según revisión. Responsable: Sin asignar.

- [ ] Revisión 1 registra pruebas reales y un PR revisado e integrado; el equipo debe completar la revisión externa.
- [ ] Revisión 2 registra cinco pruebas como mínimo, dos negativas, y dos PR nuevos integrados.
- [ ] Final registra ocho pruebas, tres negativas, incidencias corregidas e instalación reproducible.
- [ ] Solo al cerrar funcionalidades se prepara release/1.0.0, PR a master, v1.0.0 y retorno a develop.
