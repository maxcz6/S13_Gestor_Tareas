<?php
declare(strict_types=1);

namespace App\Queries;

use PDO;

final class ListarTareas
{
    public function __construct(private PDO $pdo) {}

    public function ejecutar(
        ?string $estado = null,
        ?string $prioridad = null,
        ?string $titulo = null
    ): array
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
        if ($titulo !== null && trim($titulo) !== '') {
            $condiciones[] = 'titulo LIKE :titulo ESCAPE "\\"';
            $parametros[':titulo'] = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($titulo)) . '%';
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

