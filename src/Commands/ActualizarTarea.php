<?php
declare(strict_types=1);

namespace App\Commands;

use App\Validation\TareaValidator;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class ActualizarTarea
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(int $id, array $datos): void
    {
        $errores = TareaValidator::validar($datos);
        if ($errores !== []) {
            throw new InvalidArgumentException(json_encode($errores, JSON_UNESCAPED_UNICODE));
        }

        $stmt = $this->pdo->prepare(
            'UPDATE tareas
             SET titulo = :titulo, descripcion = :descripcion, prioridad = :prioridad,
                 estado = :estado, fecha_actualizacion = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute([
            ':titulo' => trim($datos['titulo']),
            ':descripcion' => trim($datos['descripcion'] ?? ''),
            ':prioridad' => $datos['prioridad'],
            ':estado' => $datos['estado'],
            ':id' => $id,
        ]);

        if ($stmt->rowCount() === 0) {
            $existe = $this->pdo->prepare('SELECT 1 FROM tareas WHERE id = :id');
            $existe->execute([':id' => $id]);
            if (!$existe->fetchColumn()) {
                throw new RuntimeException('La tarea no existe.');
            }
        }
    }
}

