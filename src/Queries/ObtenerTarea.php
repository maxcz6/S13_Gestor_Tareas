<?php
declare(strict_types=1);

namespace App\Queries;

use PDO;

final class ObtenerTarea
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tareas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $tarea = $stmt->fetch();
        return $tarea === false ? null : $tarea;
    }
}

