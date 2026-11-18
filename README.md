# EcoReto

Proyecto 05 · Aplicaciones de Internet 2026.

Las campañas ecológicas suelen registrar participaciones manualmente y dan poca retroalimentación. EcoReto busca registrar retos, evidencias verificadas y puntos para mostrar avances individuales y por grupo.

## MVP y alcance del avance

Catálogo con fechas y puntos; participación con evidencia textual; aprobación/rechazo por moderador; puntos solo al aprobar; ranking y estadísticas por persona o grupo. Incluye CRUD de retos en revisión 2. Sin API externa, recompensas monetarias ni reconocimiento automático de fotografías.

La primera feature implementa catálogo, detalle y búsqueda PHP con seis retos semilla, filtros combinados, estados vacíos y errores recuperables. Incluye navegación responsiva y bocetos; ranking, formulario y estado pendiente son vistas iniciales explícitamente identificadas como ejemplos. Persistencia, cuentas, moderación y cálculo definitivo corresponden al equipo y siguen pendientes. No se debe confundir una maqueta con un registro guardado.

## Tecnologías y ejecución

HTML5, utilidades Tailwind por CDN durante desarrollo, Google Fonts, JavaScript nativo y PHP puro 8.x. SQLite por PDO en la etapa de persistencia. Sin Node, Composer ni frameworks.

Para ejecutar el avance antes de integrar el PR, cambia a su rama:

```sh
git clone https://github.com/MarioDiazRamos/EcoReto.git
cd EcoReto
git switch feature/catalogo-navegacion
php -v
php -r 'var_export(PDO::getAvailableDrivers());'
php -S 127.0.0.1:8000 -t public
```

Abrir http://127.0.0.1:8000. Después de la revisión e integración real, usar `develop` actualizada. `master` y `develop` conservan la base documental mientras el PR permanezca abierto. No hay base que inicializar en el primer avance; los datos son ejemplos sintéticos. SQLite se alojará en `storage/`, fuera de `public/`, y sus archivos locales se ignoran. Los estilos y fuentes necesitan internet. El servidor integrado es para desarrollo local.

## Organización

- [Historias, backlog y dependencias](docs/BACKLOG.md).
- [Modelo preliminar y reglas de puntuación](docs/MODELO.md).
- [Equipo y flujo Git](docs/EQUIPO.md).
- [Entrega y evidencia de revisión 1](docs/ENTREGA-01.md).
- [Bocetos](docs/bocetos.svg).

Fuentes verificadas: PDF `Proyectos_Finales_Aplicaciones_de_Internet_2026_Guia_Alumno.pdf`, bases generales pp. 6–8, EcoReto pp. 20–21 y entrega pp. 32–33; guías `03-SESION-01-UI-ESTATICA.md` a `06-SESION-04-CRUD-Y-RELEASE.md`. Originales disponibles en Descargas del propietario; no se redistribuyen ni se copia UniSwap GTO.

Revisiones: 19/11 (25 puntos), 26/11 (35 puntos), 03/12/2026 (40 puntos). El repositorio es inicialmente privado: el docente necesita acceso.

## Pruebas y estado de colaboración

Con el servidor iniciado:

```sh
php tests/smoke.php
python tests/http_smoke.py
```

Python solo se usa para las pruebas HTTP opcionales; la aplicación requiere PHP y un navegador. Resultados ejecutados el 17/11: 14 pruebas PHP y 38 comprobaciones HTTP correctas. Recorrieron 16 rutas internas. Sin motor de navegador en este entorno: falta comprobar visualmente el móvil, el menú y la previsualización del formulario.

[PR #8](https://github.com/MarioDiazRamos/EcoReto/pull/8): `feature/catalogo-navegacion` → `develop`. Abierto, sin revisión ni integración. Ambas ramas principales exigen PR y una aprobación; administradores conservan excepción. Falta incorporar un compañero real para revisar. No se declara cumplido el requisito del primer PR integrado.
