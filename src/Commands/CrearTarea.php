<?php
declare(strict_types=1);

namespace App\Commands;

use App\Validation\TareaValidator;
use InvalidArgumentException;
use PDO;

final class CrearTarea
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(array $datos): int
    {
        $errores = TareaValidator::validar($datos);
        if ($errores !== []) {
            throw new InvalidArgumentException(json_encode($errores, JSON_UNESCAPED_UNICODE));
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO tareas (titulo, descripcion, prioridad, estado)
             VALUES (:titulo, :descripcion, :prioridad, :estado)'
        );
        $stmt->execute([
            ':titulo' => trim($datos['titulo']),
            ':descripcion' => trim($datos['descripcion'] ?? ''),
            ':prioridad' => $datos['prioridad'],
            ':estado' => $datos['estado'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

