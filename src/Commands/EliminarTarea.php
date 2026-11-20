<?php
declare(strict_types=1);

namespace App\Commands;

use PDO;
use RuntimeException;

final class EliminarTarea
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tareas WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('La tarea no existe.');
        }
    }
}

