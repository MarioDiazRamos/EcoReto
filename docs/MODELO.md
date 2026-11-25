# Modelo de datos de EcoReto

La persistencia utiliza SQLite mediante PDO para gestionar
grupos, participantes, retos y participaciones.

Los moderadores pueden crear retos desde la interfaz.

La HU-02 implementa el registro y la consulta persistente de
participaciones, relacionando cada evidencia con un usuario
y un reto existente.

Las funcionalidades de moderación, validación de evidencias
y asignación de puntos se completarán mediante las historias
de usuario correspondientes.


## Entidades

| Entidad | Campos | Restricciones y relaciones |
|---|---|---|
| grupos | id PK, nombre | Un grupo agrupa participantes |
| participantes | id PK, nombre_visible, grupo_id FK nullable, rol, password_hash | Rol participante o moderador |
| retos | id PK, titulo, descripcion, categoria, fecha_inicio, fecha_fin, puntos, activo | Puntos enteros positivos; inicio anterior o igual al cierre |
| participaciones | id PK, reto_id FK, participante_id FK, evidencia_textual, estado, creada_en | Una participación por usuario y reto; evidencia de 10 a 1000 caracteres; estado inicial pendiente, con estados posibles aprobada y rechazada |
| validaciones | id PK, participacion_id FK UNIQUE, moderador_id FK, decision, motivo, validada_en | Una decisión final por participación; moderador distinto del autor |
| puntos | id PK, participacion_id FK UNIQUE, participante_id FK, valor, otorgados_en | Un asiento por participación aprobada |

Relaciones: grupos 1:N participantes; retos 1:N participaciones; participantes 1:N participaciones; participaciones 0:1 validaciones y 0:1 asientos de puntos.

## Implementación de HU-02: Participaciones

La historia de usuario HU-02 implementa el registro y consulta
persistente de evidencias de participación.

### Tabla participaciones

La tabla se define en `database/002_participaciones.sql`
y contiene los siguientes campos:

| Campo | Tipo | Descripción |
|---|---|---|
| id | INTEGER PRIMARY KEY | Identificador único |
| reto_id | INTEGER NOT NULL | Referencia al reto |
| participante_id | INTEGER NOT NULL | Referencia al usuario |
| evidencia_textual | TEXT NOT NULL | Evidencia de 10 a 1000 caracteres |
| estado | TEXT NOT NULL | Pendiente, aprobada o rechazada |
| creada_en | TEXT NOT NULL | Fecha y hora de creación |

### Relaciones y restricciones

- Cada participación pertenece a un reto existente.
- Cada participación pertenece a un usuario registrado.
- La restricción UNIQUE(reto_id, participante_id) impide duplicados.
- La evidencia textual debe contener entre 10 y 1000 caracteres.
- El estado inicial de una participación es pendiente.
- Las claves foráneas mantienen la integridad referencial.
- Se utiliza un índice para consultar participaciones por usuario.

### Registro de participaciones

El registro se realiza desde `public/participar.php`.

El usuario debe iniciar sesión antes de registrar una evidencia.

El servidor valida el reto, el participante y la evidencia
antes de realizar la inserción mediante consultas preparadas PDO.

Los formularios utilizan protección CSRF para evitar
solicitudes no autorizadas.

### Consulta de participaciones

Las consultas se implementan en `src/participaciones.php`.

Las funciones principales son:

- registrarParticipacion(): valida y guarda una evidencia.
- listarParticipaciones(): obtiene el historial de un usuario.
- obtenerParticipacion(): obtiene una participación específica.

La página `public/pendiente.php` permite consultar el historial
y el detalle de las participaciones registradas.

Cada usuario solamente puede consultar sus propias evidencias.

### Estados de participación

- pendiente: evidencia registrada, todavía sin validación.
- aprobada: evidencia aceptada por un moderador.
- rechazada: evidencia no aceptada por un moderador.

La HU-02 permite almacenar y visualizar estos estados.

La modificación de los estados mediante moderación
corresponde a HU-03.

La asignación de puntos corresponde a HU-06.

### Pruebas

Las pruebas de HU-02 se encuentran en:

- tests/participaciones_db.php
- tests/participaciones_funciones.php
- tests/participaciones_integracion.php

Se verifican persistencia, validaciones, privacidad,
prevención de duplicados e integración con autenticación.

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
