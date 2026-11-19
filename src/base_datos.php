<?php
declare(strict_types=1);

namespace EcoReto;

function abrirBaseDatos(): \PDO
{
    static $db = null;
    if ($db instanceof \PDO) {
        return $db;
    }

    $ruta = dirname(__DIR__) . '/storage/ecoreto.sqlite';
    $db = new \PDO('sqlite:' . $ruta);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON');

    $db->exec(
        'CREATE TABLE IF NOT EXISTS grupos (
            id INTEGER PRIMARY KEY,
            nombre TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS participantes (
            id INTEGER PRIMARY KEY,
            usuario TEXT NOT NULL COLLATE NOCASE UNIQUE,
            nombre_visible TEXT NOT NULL,
            grupo_id INTEGER REFERENCES grupos(id),
            rol TEXT NOT NULL DEFAULT \'participante\'
                CHECK (rol IN (\'participante\', \'moderador\')),
            password_hash TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS retos (
            id INTEGER PRIMARY KEY,
            titulo TEXT NOT NULL,
            descripcion TEXT NOT NULL,
            categoria TEXT NOT NULL,
            fecha_inicio TEXT NOT NULL,
            fecha_fin TEXT NOT NULL,
            puntos INTEGER NOT NULL CHECK (puntos > 0),
            evidencia TEXT NOT NULL,
            activo INTEGER NOT NULL DEFAULT 1 CHECK (activo IN (0, 1))
        );'
    );

    $semillas = require __DIR__ . '/retos.php';
    $insertar = $db->prepare(
        'INSERT INTO retos
            (id, titulo, descripcion, categoria, fecha_inicio, fecha_fin, puntos, evidencia, activo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
         ON CONFLICT(id) DO NOTHING'
    );
    $db->beginTransaction();
    foreach ($semillas as $reto) {
        $insertar->execute([
            $reto['id'],
            $reto['titulo'],
            $reto['descripcion'],
            $reto['categoria'],
            $reto['inicio'],
            $reto['fin'],
            $reto['puntos'],
            $reto['evidencia'],
        ]);
    }
    $db->commit();

    return $db;
}
