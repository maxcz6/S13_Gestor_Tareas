<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Commands\ActualizarTarea;
use App\Commands\CrearTarea;
use App\Commands\EliminarTarea;
use App\Queries\ListarTareas;
use App\Queries\ObtenerTarea;

$ruta = sys_get_temp_dir() . '/tareas_prueba_' . bin2hex(random_bytes(4)) . '.sqlite';

try {
    $pdo = crearConexion($ruta);
    $id = (new CrearTarea($pdo))->ejecutar([
        'titulo' => 'Preparar evidencia de práctica',
        'descripcion' => 'Registrar el resultado del CRUD.',
        'prioridad' => 'alta',
        'estado' => 'pendiente',
    ]);

    assert($id > 0);
    assert(count((new ListarTareas($pdo))->ejecutar()) === 1);
    assert(count((new ListarTareas($pdo))->ejecutar(null, null, 'evidencia')) === 1);
    assert(count((new ListarTareas($pdo))->ejecutar(null, null, 'inexistente')) === 0);
    assert((new ObtenerTarea($pdo))->ejecutar($id)['titulo'] === 'Preparar evidencia de práctica');

    (new ActualizarTarea($pdo))->ejecutar($id, [
        'titulo' => 'Preparar evidencia de práctica',
        'descripcion' => 'CRUD verificado.',
        'prioridad' => 'media',
        'estado' => 'completada',
    ]);
    assert((new ObtenerTarea($pdo))->ejecutar($id)['estado'] === 'completada');

    (new EliminarTarea($pdo))->ejecutar($id);
    assert((new ObtenerTarea($pdo))->ejecutar($id) === null);
    echo "OK: crear, consultar, actualizar y eliminar.\n";
} finally {
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

