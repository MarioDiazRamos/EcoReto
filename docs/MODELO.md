# Modelo de datos preliminar

La persistencia se implementará con SQLite y PDO. El avance actual consulta un catálogo de ejemplo; todavía no dispone de esquema SQL ni almacenamiento de participaciones.

## Entidades

| Entidad | Campos | Restricciones y relaciones |
|---|---|---|
| grupos | id PK, nombre | Un grupo agrupa participantes |
| participantes | id PK, nombre_visible, grupo_id FK nullable, rol, password_hash | Rol participante o moderador |
| retos | id PK, titulo, descripcion, categoria, fecha_inicio, fecha_fin, puntos, activo | Puntos enteros positivos; inicio anterior o igual al cierre |
| participaciones | id PK, reto_id FK, participante_id FK, evidencia_textual, estado, creada_en | Estado inicial pendiente; estados finales aprobada o rechazada |
| validaciones | id PK, participacion_id FK UNIQUE, moderador_id FK, decision, motivo, validada_en | Una decisión final por participación; moderador distinto del autor |
| puntos | id PK, participacion_id FK UNIQUE, participante_id FK, valor, otorgados_en | Un asiento por participación aprobada |

Relaciones: grupos 1:N participantes; retos 1:N participaciones; participantes 1:N participaciones; participaciones 0:1 validaciones y 0:1 asientos de puntos.

## Reglas de puntuación y moderación

1. Una participación pendiente o rechazada aporta cero puntos.
2. Solo un moderador puede aprobar o rechazar; no puede validar su propia evidencia.
3. La aprobación asigna puntos una sola vez, incluso al repetir la solicitud.
4. El valor otorgado se obtiene del reto en el servidor y se conserva en `puntos.valor`.
5. La aprobación y el asiento de puntos se guardan en la misma transacción; un fallo revierte ambos cambios.
6. El ranking suma los asientos asociados con participaciones aprobadas, por persona o grupo.

## Integridad y acceso

Activar claves foráneas con `PRAGMA foreign_keys=ON`. Utilizar consultas preparadas PDO, validación en servidor y una restricción única por participación en la tabla de puntos. Comprobar la transición desde pendiente antes de registrar la decisión.

Las operaciones de escritura requieren autorización y protección CSRF. La autenticación utilizará sesiones y `password_hash()` / `password_verify()`. La base local estará en `storage/`, fuera de la raíz pública y excluida de Git.

## Políticas propuestas

- Permitir una participación por persona y reto.
- Compartir posición en el ranking cuando haya empate.
- Desactivar los retos con participaciones para conservar el historial.

## Datos de demostración

El catálogo contiene seis retos de ejemplo con fechas fijas de noviembre y diciembre de 2026. El ranking inicial utiliza personas y grupos ficticios; sus valores todavía no proceden de validaciones persistentes.
