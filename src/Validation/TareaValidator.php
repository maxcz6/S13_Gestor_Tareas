<?php
declare(strict_types=1);

namespace App\Validation;

final class TareaValidator
{
    private const PRIORIDADES = ['baja', 'media', 'alta'];
    private const ESTADOS = ['pendiente', 'en_proceso', 'completada'];

    public static function validar(array $datos): array
    {
        $errores = [];
        $titulo = trim((string) ($datos['titulo'] ?? ''));
        $descripcion = trim((string) ($datos['descripcion'] ?? ''));
        $prioridad = (string) ($datos['prioridad'] ?? '');
        $estado = (string) ($datos['estado'] ?? '');

        $longitudTitulo = mb_strlen($titulo);
        if ($longitudTitulo < 5 || $longitudTitulo > 100) {
            $errores['titulo'] = 'El título debe tener entre 5 y 100 caracteres.';
        }
        if (mb_strlen($descripcion) > 500) {
            $errores['descripcion'] = 'La descripción no debe superar 500 caracteres.';
        }
        if (!in_array($prioridad, self::PRIORIDADES, true)) {
            $errores['prioridad'] = 'Seleccione una prioridad permitida.';
        }
        if (!in_array($estado, self::ESTADOS, true)) {
            $errores['estado'] = 'Seleccione un estado permitido.';
        }

        return $errores;
    }
}

