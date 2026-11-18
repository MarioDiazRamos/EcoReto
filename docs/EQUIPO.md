# Equipo y colaboración

Cuenta propietaria confirmada por API: **MarioDiazRamos** (Mario Diaz Ramos). Al inspeccionar colaboradores solo aparece el propietario con rol admin. No se conoce todavía el número ni las cuentas de otros integrantes. No hay revisores externos confirmados.

Mario se encarga del arranque y HU-01. El código generado con asistencia se registra con la identidad verificada del propietario; esto no demuestra contribuciones manuales de otros integrantes. Las demás historias quedan sin responsable definitivo.

## Propuesta de reparto

Distribuir funcionalidades completas y ponderar carga, no cantidad de archivos. Cuando se conozcan las cuentas, repartir HU-02 (participaciones), HU-03 (roles/moderación), HU-04 (CRUD), HU-05 (ranking/estadísticas) y HU-06 (puntos) entre las personas disponibles. HU-03 y HU-06 necesitan coordinar transacción y autorización; HU-05 consume sus resultados. La carga de HU-03 puede compensarse con menos tareas secundarias. HU-07 y las revisiones son compartidas. Si hay menos personas, agrupar módulos relacionados; si hay más, separar criterios coherentes mediante acuerdo antes de abrir ramas. Nadie recibe una asignación inventada.

Para incorporar al equipo: el propietario abre Settings → Collaborators del repositorio privado e invita las cuentas reales con acceso Write; cada persona acepta la invitación. Invitar al docente según el acceso requerido para evaluar. Después asignar Issues y solicitar revisores reales. No se enviaron invitaciones sin identificar destinatarios.

## Flujo obligatorio

```sh
git switch develop
git pull --ff-only origin develop
git switch -c feature/descripcion
# Implementar y comprobar una funcionalidad coherente
git add rutas-revisadas
git commit -m "feat: describir comportamiento"
git push -u origin feature/descripcion
```

Un PR por funcionalidad hacia **develop**; documentar historia, cambios, pruebas reales, evidencia y pendientes. No crear PR por archivo o cambio trivial ni concentrar todo el MVP en uno. No iniciar features desde master u otra feature. No inventar revisiones ni aprobar con cuentas ajenas. Un integrante diferente debe revisar antes de integrar; si solo está el propietario registrado, esta entrega mantiene el PR abierto y el requisito pendiente.

master es estable; develop integra. No hacer desarrollo directo en master. Más adelante `release/1.0.0` nace de develop, PR a master, tag v1.0.0 y correcciones de vuelta a develop por PR. No crear release en este avance. Revisión 1 exige un PR integrado; revisión 2, dos PR nuevos adicionales. Conservar la autoría real, sin atribuir a compañeros código que no hicieron.

Las protecciones dependen del plan GitHub. Si no están disponibles, aplicar estas reglas mediante revisión real y registrar la limitación en la entrega; no presentarlas como enforcement automático.
