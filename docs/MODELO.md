# Modelo preliminar y reglas

Propuesta para discutir antes de persistir; todavía no existe esquema SQL ni autenticación implementada.

| Entidad | Campos propuestos | Restricciones / relaciones |
|---|---|---|
| grupos | id PK, nombre | Un grupo puede tener varios participantes |
| participantes | id PK, nombre_visible, grupo_id FK nullable, rol, password_hash | rol participante/moderador; credenciales nunca en semillas públicas |
| retos | id PK, titulo, descripcion, categoria, fecha_inicio, fecha_fin, puntos, activo | puntos entero positivo; inicio <= fin |
| participaciones | id PK, reto_id FK, participante_id FK, evidencia_textual, estado, creada_en | pendiente/aprobada/rechazada; inicia pendiente |
| validaciones | id PK, participacion_id FK UNIQUE, moderador_id FK, decision, motivo, validada_en | moderador distinto del autor; una decisión final por participación |
| puntos | id PK, participacion_id FK UNIQUE, participante_id FK, valor, otorgados_en | Solo se inserta por aprobación dentro de la misma transacción |

Relaciones: grupos 1:N participantes; retos y participantes 1:N participaciones; participación 0:1 validación y 0:1 asiento de puntos. Usar claves foráneas con `PRAGMA foreign_keys=ON`, PDO preparado, validación servidor y transacciones. `puntos.valor` conserva el valor otorgado aunque cambie el reto. El ranking suma asientos aprobados; no almacena otro total mutable.

## Invariantes obligatorias

1. Pendiente o rechazada aporta **cero** puntos.
2. Aprobar de nuevo la misma participación no duplica asientos ni puntos: transición condicional desde pendiente y UNIQUE(participacion_id), en una transacción.
3. Nadie puede aprobar su propia evidencia. Comprobar rol y autoría en servidor, incluso ante POST manipulado.
4. Los puntos se toman del reto en servidor; nunca de valores enviados por el cliente.
5. Aprobar y asignar puntos debe ser atómico; si falla cualquier paso se revierte todo.

Propuestas de política a validar con el equipo: una participación por persona y reto; empate de ranking comparte posición; retos con participación se desactivan para conservar historial. No se presentan estas decisiones como requisitos académicos adicionales.

Seguridad futura: sesiones, password_hash/password_verify, CSRF para escrituras y autorización de cada acción sensible. No se ofrecerán acciones de moderación sin implementar esos controles.

## Datos del primer avance

El catálogo usa retos sintéticos de demostración. El ranking inicial es un ejemplo visual y no prueba moderación ni cálculo persistente. No hay personas reales en las semillas. Las fechas de demostración son fijas y están identificadas como tales.
