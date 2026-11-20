<?php
declare(strict_types=1);

namespace App\Queries;

use PDO;

final class ListarTareas
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(?string $estado = null, ?string $prioridad = null): array
    {
        $condiciones = [];
        $parametros = [];

        if ($estado !== null && $estado !== '') {
            $condiciones[] = 'estado = :estado';
            $parametros[':estado'] = $estado;
        }
        if ($prioridad !== null && $prioridad !== '') {
            $condiciones[] = 'prioridad = :prioridad';
            $parametros[':prioridad'] = $prioridad;
        }

        $sql = 'SELECT * FROM tareas';
        if ($condiciones !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $sql .= ' ORDER BY id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }
}

