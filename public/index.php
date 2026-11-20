<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Commands\ActualizarTarea;
use App\Commands\CrearTarea;
use App\Commands\EliminarTarea;
use App\Queries\ListarTareas;
use App\Queries\ObtenerTarea;
$pdo = crearConexion();
$accion = $_GET['accion'] ?? 'listar';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$mensaje = $_GET['mensaje'] ?? '';
$error = '';
$datosFormulario = ['titulo' => '', 'descripcion' => '', 'prioridad' => 'media', 'estado' => 'pendiente'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf'] ?? '';
        session_start();
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            throw new RuntimeException('La solicitud no es válida. Actualice la página.');
        }

        if ($accion === 'crear') {
            $nuevoId = (new CrearTarea($pdo))->ejecutar($_POST);
            header('Location: ?accion=detalle&id=' . $nuevoId . '&mensaje=creada');
            exit;
        }
        if ($accion === 'editar' && $id !== null) {
            (new ActualizarTarea($pdo))->ejecutar($id, $_POST);
            header('Location: ?accion=detalle&id=' . $id . '&mensaje=actualizada');
            exit;
        }
        if ($accion === 'eliminar' && $id !== null) {
            (new EliminarTarea($pdo))->ejecutar($id);
            header('Location: ?mensaje=eliminada');
            exit;
        }
    }
} catch (InvalidArgumentException | RuntimeException $e) {
    $error = $e->getMessage();
    $datosFormulario = array_merge($datosFormulario, $_POST);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$tarea = null;
if (($accion === 'detalle' || $accion === 'editar') && $id !== null) {
    $tarea = (new ObtenerTarea($pdo))->ejecutar($id);
    if ($tarea === null) {
        http_response_code(404);
        $error = 'La tarea solicitada no existe.';
        $accion = 'listar';
    } elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $datosFormulario = $tarea;
    }
}

$estadoFiltro = $_GET['estado'] ?? '';
$prioridadFiltro = $_GET['prioridad'] ?? '';
$tituloFiltro = $_GET['titulo'] ?? '';
$tareas = (new ListarTareas($pdo))->ejecutar($estadoFiltro, $prioridadFiltro, $tituloFiltro);

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestor de tareas</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
    <div class="contenedor encabezado">
        <div><h1>Gestor de tareas</h1><p>PHP, SQLite y CQRS básico</p></div>
        <a class="boton" href="?accion=crear">Nueva tarea</a>
    </div>
</header>
<main class="contenedor">
    <?php if ($mensaje !== ''): ?><p class="mensaje">Operación completada: <?= e($mensaje) ?>.</p><?php endif; ?>
    <?php if ($error !== ''): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <?php if ($accion === 'crear' || $accion === 'editar'): ?>
        <section class="panel">
            <h2><?= $accion === 'crear' ? 'Registrar tarea' : 'Editar tarea' ?></h2>
            <form method="post" action="?accion=<?= e($accion) ?><?= $id ? '&id=' . $id : '' ?>">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <label>Título<input name="titulo" minlength="5" maxlength="100" required value="<?= e((string) $datosFormulario['titulo']) ?>"></label>
                <label>Descripción<textarea name="descripcion" maxlength="500"><?= e((string) $datosFormulario['descripcion']) ?></textarea></label>
                <div class="fila">
                    <label>Prioridad<select name="prioridad" required><?php foreach (['baja','media','alta'] as $v): ?><option value="<?= $v ?>" <?= $datosFormulario['prioridad'] === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option><?php endforeach; ?></select></label>
                    <label>Estado<select name="estado" required><?php foreach (['pendiente','en_proceso','completada'] as $v): ?><option value="<?= $v ?>" <?= $datosFormulario['estado'] === $v ? 'selected' : '' ?>><?= e(str_replace('_', ' ', ucfirst($v))) ?></option><?php endforeach; ?></select></label>
                </div>
                <div class="acciones"><button class="boton" type="submit">Guardar</button><a href="?">Cancelar</a></div>
            </form>
        </section>
    <?php elseif ($accion === 'detalle' && $tarea): ?>
        <section class="panel detalle">
            <div><span class="etiqueta <?= e($tarea['prioridad']) ?>"><?= e($tarea['prioridad']) ?></span><span class="etiqueta estado"><?= e($tarea['estado']) ?></span></div>
            <h2><?= e($tarea['titulo']) ?></h2>
            <p><?= nl2br(e($tarea['descripcion'])) ?></p>
            <p class="secundario">Creada: <?= e($tarea['fecha_creacion']) ?> | Actualizada: <?= e($tarea['fecha_actualizacion']) ?></p>
            <div class="acciones"><a class="boton" href="?accion=editar&id=<?= $tarea['id'] ?>">Editar</a><a href="?">Volver</a></div>
        </section>
    <?php else: ?>
        <section class="panel">
            <h2>Tareas registradas</h2>
            <form class="filtros" method="get">
                <input type="search" name="titulo" value="<?= e((string) $tituloFiltro) ?>" placeholder="Buscar por título" aria-label="Buscar por título">
                <select name="estado"><option value="">Todos los estados</option><?php foreach (['pendiente','en_proceso','completada'] as $v): ?><option value="<?= $v ?>" <?= $estadoFiltro === $v ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $v)) ?></option><?php endforeach; ?></select>
                <select name="prioridad"><option value="">Todas las prioridades</option><?php foreach (['baja','media','alta'] as $v): ?><option value="<?= $v ?>" <?= $prioridadFiltro === $v ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
                <button type="submit">Filtrar</button><a href="?">Limpiar</a>
            </form>
            <?php if ($tareas === []): ?><p>No existen tareas con los filtros seleccionados.</p><?php endif; ?>
            <div class="tarjetas">
                <?php foreach ($tareas as $item): ?>
                    <article class="tarjeta">
                        <div><span class="etiqueta <?= e($item['prioridad']) ?>"><?= e($item['prioridad']) ?></span><span class="etiqueta estado"><?= e($item['estado']) ?></span></div>
                        <h3><?= e($item['titulo']) ?></h3>
                        <p><?= e(mb_strimwidth($item['descripcion'], 0, 120, '...')) ?></p>
                        <div class="acciones">
                            <a href="?accion=detalle&id=<?= $item['id'] ?>">Ver</a>
                            <a href="?accion=editar&id=<?= $item['id'] ?>">Editar</a>
                            <form method="post" action="?accion=eliminar&id=<?= $item['id'] ?>" onsubmit="return confirm('¿Eliminar esta tarea?')">
                                <input type="hidden" name="csrf" value="<?= e($csrf) ?>"><button class="enlace-peligro" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>

