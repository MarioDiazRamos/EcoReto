<?php
declare(strict_types=1);

// HU-02: Pruebas de integración con SQLite.
// Utilizamos una base de datos temporal para no
// modificar los datos reales de EcoReto.

require_once dirname(__DIR__) . '/src/autenticacion.php';
require_once dirname(__DIR__) . '/src/participaciones.php';
require_once dirname(__DIR__) . '/src/catalogo.php';

use function EcoReto\{
    autenticar,
    registrarParticipacion,
    listarParticipaciones,
    obtenerParticipacion,
    escapar
};

/**
 * Comprueba una condición e informa el resultado.
 */
function verificarIntegracion(
    bool $condicion,
    string $mensaje
): void {
    if (!$condicion) {
        throw new RuntimeException('FALLO: ' . $mensaje);
    }

    echo 'OK: ' . $mensaje . PHP_EOL;
}

/**
 * Ejecuta todas las pruebas dentro de una función.
 *
 * Esto permite liberar las conexiones SQLite
 * antes de eliminar el archivo temporal en Windows.
 */
function ejecutarPruebasHU02(string $ruta): void
{

    $db = new PDO('sqlite:' . $ruta);

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $db->exec('PRAGMA foreign_keys = ON');

    $raiz = dirname(__DIR__);

    $sqlIdentidad = file_get_contents(
        $raiz . '/database/001_identidad.sql'
    );

    $sqlParticipaciones = file_get_contents(
        $raiz . '/database/002_participaciones.sql'
    );

    if (
        $sqlIdentidad === false
        || $sqlParticipaciones === false
    ) {
        throw new RuntimeException(
            'No se pudieron leer los archivos SQL.'
        );
    }

    // Crear las tablas del proyecto
    $db->exec($sqlIdentidad);
    $db->exec($sqlParticipaciones);
    $db->exec(file_get_contents($raiz . '/database/003_moderacion.sql'));

    $hash = password_hash(
        'clave-prueba-hu02',
        PASSWORD_DEFAULT
    );

    $insertar = $db->prepare(
        'INSERT INTO participantes
         (usuario, nombre_visible, password_hash)
         VALUES (?, ?, ?)'
    );

    $insertar->execute([
        'ana',
        'Ana',
        $hash
    ]);

    $insertar->execute([
        'luis',
        'Luis',
        $hash
    ]);

    // Liberar la consulta preparada
    $insertar = null;

    $db->exec(
        "INSERT INTO retos
         (id, titulo, descripcion, categoria,
          fecha_inicio, fecha_fin, puntos, evidencia, activo)
         VALUES
         (1, 'Reto de prueba', 'Descripción de prueba',
          'Residuos', '2026-11-01', '2026-12-31',
          20, 'Evidencia textual', 1)"
    );

    // PRUEBA 1: AUTENTICACIÓN CORRECTA
    $ana = autenticar(
        $db,
        'ana',
        'clave-prueba-hu02'
    );

    $luis = autenticar(
        $db,
        'luis',
        'clave-prueba-hu02'
    );

    verificarIntegracion(
        $ana !== null && $luis !== null,
        'Usuarios autenticados correctamente'
    );

    // PRUEBA 2: CONTRASEÑA INCORRECTA
    verificarIntegracion(
        autenticar(
            $db,
            'ana',
            'incorrecta'
        ) === null,
        'Se rechazan credenciales incorrectas'
    );

    // PRUEBA 3: REGISTRAR PARTICIPACIÓN
    $evidencia = 'Reciclé papel y plástico durante la semana.';

    $id = registrarParticipacion(
        $db,
        1,
        $ana['participante_id'],
        $evidencia
    );

    verificarIntegracion(
        $id > 0,
        'Participación registrada correctamente'
    );

    // PRUEBA 4: ESTADO INICIAL PENDIENTE
    $participacion = obtenerParticipacion(
        $db,
        $id,
        $ana['participante_id']
    );

    verificarIntegracion(
        $participacion !== null
        && $participacion['estado'] === 'pendiente',
        'La participación inicia como pendiente'
    );

    // PRUEBA 5: CONSULTAR HISTORIAL
    $historial = listarParticipaciones(
        $db,
        $ana['participante_id']
    );

    verificarIntegracion(
        count($historial) === 1,
        'El historial muestra la participación'
    );

    // PRUEBA 6: PRIVACIDAD ENTRE USUARIOS
    $historialLuis = listarParticipaciones(
        $db,
        $luis['participante_id']
    );

    verificarIntegracion(
        count($historialLuis) === 0,
        'Un usuario no ve participaciones ajenas'
    );

    // PRUEBA 7: PROTEGER EL DETALLE
    verificarIntegracion(
        obtenerParticipacion(
            $db,
            $id,
            $luis['participante_id']
        ) === null,
        'No se permite consultar un detalle ajeno'
    );

    // PRUEBA 8: EVITAR DUPLICADOS
    $duplicadoRechazado = false;

    try {
        registrarParticipacion(
            $db,
            1,
            $ana['participante_id'],
            'Intento de registrar una segunda evidencia.'
        );
    } catch (DomainException $e) {
        $duplicadoRechazado = true;
    }

    verificarIntegracion(
        $duplicadoRechazado,
        'Se rechazan participaciones duplicadas'
    );

    // PRUEBA 9: PROTECCIÓN CONTRA HTML
    $textoPeligroso = '<script>alert(1)</script>';

    $textoSeguro = escapar($textoPeligroso);

    verificarIntegracion(
        !str_contains(
            $textoSeguro,
            '<script>'
        )
        && str_contains(
            $textoSeguro,
            '&lt;script&gt;'
        ),
        'Se escapa el HTML de las evidencias'
    );
    // PRUEBA 10: PERSISTENCIA EN SQLITE
    // Cerrar la primera conexión.
    $db = null;

    // Volver a abrir la misma base temporal.
    $db = new PDO('sqlite:' . $ruta);

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $consulta = $db->prepare(
        'SELECT evidencia_textual, estado
         FROM participaciones
         WHERE id = ?'
    );

    $consulta->execute([$id]);

    $guardada = $consulta->fetch(
        PDO::FETCH_ASSOC
    );

    verificarIntegracion(
        $guardada !== false
        && $guardada['evidencia_textual'] === $evidencia
        && $guardada['estado'] === 'pendiente',
        'Los datos persisten después de reabrir SQLite'
    );

    // Cerrar la última consulta preparada.
    $consulta = null;

    // Cerrar la conexión SQLite.
    $db = null;

    // Al finalizar esta función, también se
    // liberarán todas sus variables locales.
}

$ruta = tempnam(
    sys_get_temp_dir(),
    'ecoreto_hu02_'
);

if ($ruta === false) {
    throw new RuntimeException(
        'No se pudo crear la base temporal.'
    );
}

try {

    // Ejecutar las 10 pruebas.
    ejecutarPruebasHU02($ruta);

} finally {

    // Las variables locales de la función
    // ya se liberaron antes de llegar aquí.
    gc_collect_cycles();

    // Windows puede tardar un instante
    // en liberar un archivo SQLite.
    // Reintentamos su eliminación hasta 10 veces.
    for (
        $intento = 0;
        $intento < 10 && is_file($ruta);
        $intento++
    ) {

        if (@unlink($ruta)) {
            break;
        }

        // Esperar 100 milisegundos antes
        // del siguiente intento.
        usleep(100000);
    }

    // Si el archivo sigue existiendo,
    // informar claramente el problema.
    if (is_file($ruta)) {
        throw new RuntimeException(
            'No se pudo eliminar la base temporal: '
            . $ruta
        );
    }
}

echo PHP_EOL;
echo 'Todas las pruebas de integración HU-02 pasaron.'
    . PHP_EOL;
