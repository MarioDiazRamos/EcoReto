# Equipo y colaboración

## Integrantes y responsabilidades

| Integrante | Cuenta | Responsabilidad |
|---|---|---|
| Mario Diaz Ramos | [MarioDiazRamos](https://github.com/MarioDiazRamos) | HU-01: catálogo, búsqueda y navegación inicial; documentación de la primera entrega |

## Distribución de tareas

| Área | Historias | Responsable |
|---|---|---|
| Catálogo y navegación | HU-01 | MarioDiazRamos |
| Participaciones persistentes | HU-02 | Por asignar |
| Autenticación y moderación | HU-03 | Por asignar |
| Administración de retos | HU-04 | Por asignar |
| Ranking y estadísticas | HU-05 | Por asignar |
| Asignación de puntos | HU-06 | Por asignar |
| Pruebas e integración de entregas | HU-07 | Por asignar |

Cada integrante asume funcionalidades completas con sus pruebas y documentación. Moderación y puntos requieren coordinar las transacciones; ranking consume sus resultados. La asignación se acuerda según la carga y disponibilidad de los integrantes y se registra en los Issues.

## Convenciones de ramas

- `master`: versión estable; recibe los PR de release.
- `develop`: integración de funcionalidades.
- `feature/descripcion`: una funcionalidad coherente, iniciada desde develop actualizada.
- `release/1.0.0`: estabilización de la entrega final, iniciada desde develop.

```sh
git switch develop
git pull --ff-only origin develop
git switch -c feature/descripcion
```

Los commits deben describir cambios significativos y conservar la autoría de cada contribución.

## Pull Requests y revisión

1. Publicar la feature y abrir un PR hacia `develop`.
2. Relacionar la historia, describir el comportamiento e incluir pruebas, evidencia y pendientes técnicos.
3. Solicitar revisión a otro integrante y atender sus observaciones.
4. Integrar después de la aprobación y de comprobar las pruebas.
5. Actualizar develop antes de iniciar otra funcionalidad.

Mantener un PR por funcionalidad, sin dividir cambios triviales en PR separados. No integrar directamente sobre master ni aprobar el propio PR. Las ramas master y develop requieren PR y una aprobación.

## Integración de la versión final

Crear `release/1.0.0` desde develop para estabilizar la entrega. Abrir el PR hacia master, crear la etiqueta `v1.0.0` y reintegrar las correcciones a develop mediante PR.
