<?php
declare(strict_types=1);

function crearConexion(?string $ruta = null): PDO
{
    $ruta ??= __DIR__ . '/../database/tareas.sqlite';
    $directorio = dirname($ruta);

    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $pdo = new PDO('sqlite:' . $ruta);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS tareas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL CHECK(length(titulo) BETWEEN 5 AND 100),
            descripcion TEXT NOT NULL DEFAULT "",
            prioridad TEXT NOT NULL CHECK(prioridad IN ("baja", "media", "alta")),
            estado TEXT NOT NULL CHECK(estado IN ("pendiente", "en_proceso", "completada")),
            fecha_creacion TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    return $pdo;
}

