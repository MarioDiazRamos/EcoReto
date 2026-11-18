# EcoReto

Proyecto 05 · Aplicaciones de Internet 2026.

Las campañas ecológicas suelen registrar participaciones manualmente y dan poca retroalimentación. EcoReto busca registrar retos, evidencias verificadas y puntos para mostrar avances individuales y por grupo.

## MVP y alcance del avance

Catálogo con fechas y puntos; participación con evidencia textual; aprobación/rechazo por moderador; puntos solo al aprobar; ranking y estadísticas por persona o grupo. Incluye CRUD de retos en revisión 2. Sin API externa, recompensas monetarias ni reconocimiento automático de fotografías.

Este commit inicial organiza el proyecto. La primera feature implementará catálogo, detalle y búsqueda PHP con datos semilla, navegación responsiva y bocetos de las vistas requeridas. Persistencia, cuentas, moderación y cálculo definitivo corresponden al equipo y siguen pendientes. No se debe confundir una maqueta con un registro guardado.

## Tecnologías y ejecución

HTML5, utilidades Tailwind por CDN durante desarrollo, Google Fonts, JavaScript nativo y PHP puro 8.x. SQLite por PDO en la etapa de persistencia. Sin Node, Composer ni frameworks.

Tras integrar la primera feature:

```sh
git clone https://github.com/MarioDiazRamos/EcoReto.git
cd EcoReto
git switch develop
php -v
php -r 'var_export(PDO::getAvailableDrivers());'
php -S 127.0.0.1:8000 -t public
```

Abrir http://127.0.0.1:8000. No hay base que inicializar en el primer avance; los datos son ejemplos sintéticos. SQLite se alojará en `storage/`, fuera de `public/`, y sus archivos locales se ignoran. Los estilos y fuentes necesitan internet. El servidor integrado es para desarrollo local.

## Organización

- [Historias, backlog y dependencias](docs/BACKLOG.md).
- [Modelo preliminar y reglas de puntuación](docs/MODELO.md).
- [Equipo y flujo Git](docs/EQUIPO.md).
- [Entrega y evidencia de revisión 1](docs/ENTREGA-01.md).
- [Bocetos](docs/bocetos.svg).

Fuentes verificadas: PDF `Proyectos_Finales_Aplicaciones_de_Internet_2026_Guia_Alumno.pdf`, bases generales pp. 6–8, EcoReto pp. 20–21 y entrega pp. 32–33; guías `03-SESION-01-UI-ESTATICA.md` a `06-SESION-04-CRUD-Y-RELEASE.md`. Originales disponibles en Descargas del propietario; no se redistribuyen ni se copia UniSwap GTO.

Revisiones: 19/11 (25 puntos), 26/11 (35 puntos), 03/12/2026 (40 puntos). El repositorio es inicialmente privado: el docente necesita acceso.
