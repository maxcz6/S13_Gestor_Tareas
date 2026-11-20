# Gestor de tareas - Práctica N.º 13

Aplicación web desarrollada en PHP con SQLite para practicar persistencia de datos, operaciones CRUD y una separación básica de responsabilidades CQRS.

## Integrantes

- Equipo: completar.
- Integrantes: completar.

## Contenido

- [Requisitos](#requisitos)
- [Ejecución con Laravel Herd](#ejecución-con-laravel-herd)
- [Funcionalidades](#funcionalidades)
- [Operaciones CRUD y rutas](#operaciones-crud-y-rutas)
- [Arquitectura CQRS básica](#arquitectura-cqrs-básica)
- [Validaciones](#validaciones)
- [Ampliación implementada](#ampliación-implementada)
- [Prueba automática](#prueba-automática)
- [Flujo de trabajo Git](#flujo-de-trabajo-git)
- [CI/CD](#cicd)
- [Conclusiones](#conclusiones)

## Requisitos

- Windows.
- Laravel Herd.
- PHP 8.1 o superior administrado por Herd.
- Extensiones `pdo_sqlite` y `mbstring`.
- PowerShell, Git y navegador web.

El proyecto usa PHP puro. No requiere Laravel Framework, Composer, Node.js ni un servidor de base de datos independiente.

## Ejecución con Laravel Herd

1. Instale e inicie Laravel Herd.
2. En **Settings > PHP**, instale PHP 8.3 o superior y habilite `pdo_sqlite` y `mbstring`.
3. Abra una terminal nueva en la raíz del proyecto y confirme el entorno:

```powershell
herd php -v
herd php -m | Select-String "pdo_sqlite|mbstring"
```

La comprobación debe mostrar `pdo_sqlite` y `mbstring`.

4. Enlace la carpeta pública:

```powershell
cd public
herd link gestor-tareas-s13
```

5. Abra la aplicación:

```text
http://gestor-tareas-s13.test
```

La base SQLite se crea automáticamente en `database/tareas.sqlite` al iniciar la aplicación.

Para retirar el sitio al finalizar:

```powershell
cd ..
herd unlink gestor-tareas-s13
```

### Solución de problemas

- Si Herd no encuentra PHP, instale una versión desde **Settings > PHP** y seleccione PHP 8.3 o superior.
- Si `herd` no se reconoce, confirme que `%USERPROFILE%\.config\herd\bin` esté en el `Path` y abra una terminal nueva.
- Si falta una extensión, habilítela en la configuración de Herd y vuelva a comprobarla.

## Funcionalidades

- Crear tareas con título, descripción, prioridad y estado.
- Consultar el listado y el detalle de una tarea.
- Actualizar una tarea existente.
- Eliminar una tarea mediante confirmación y token CSRF.
- Filtrar por estado y prioridad.
- Buscar por coincidencia parcial en el título.
- Persistir registros en SQLite.
- Controlar datos inválidos e identificadores inexistentes.

## Operaciones CRUD y rutas

| Operación | Ruta o método | Componente |
| --- | --- | --- |
| Crear | `?accion=crear`, formulario `POST` | `src/Commands/CrearTarea.php` |
| Listar y filtrar | `?`, `?estado=pendiente&prioridad=alta&titulo=texto` | `src/Queries/ListarTareas.php` |
| Consultar detalle | `?accion=detalle&id=1` | `src/Queries/ObtenerTarea.php` |
| Actualizar | `?accion=editar&id=1`, formulario `POST` | `src/Commands/ActualizarTarea.php` |
| Eliminar | `?accion=eliminar&id=1`, formulario `POST` | `src/Commands/EliminarTarea.php` |

Los comandos modifican SQLite mediante `INSERT`, `UPDATE` y `DELETE`. Las consultas usan `SELECT` y no modifican el estado.

## Arquitectura CQRS básica

```text
src/Commands/
  CrearTarea.php
  ActualizarTarea.php
  EliminarTarea.php

src/Queries/
  ListarTareas.php
  ObtenerTarea.php

src/Validation/
  TareaValidator.php
```

Esta práctica utiliza una separación básica en carpetas sobre una sola base SQLite. Las operaciones de escritura están agrupadas como comandos y las operaciones de lectura como consultas.

## Validaciones

PHP aplica las siguientes reglas antes de ejecutar las operaciones:

- El título debe tener entre 5 y 100 caracteres.
- La descripción no puede superar 500 caracteres.
- La prioridad debe ser `baja`, `media` o `alta`.
- El estado debe ser `pendiente`, `en_proceso` o `completada`.
- Una actualización o eliminación rechaza identificadores inexistentes.

SQLite respalda las restricciones principales mediante `CHECK` y `NOT NULL`.

Casos de ejemplo:

| Caso | Resultado |
| --- | --- |
| Título `A` | PHP rechaza la creación y no ejecuta el `INSERT`. |
| Prioridad `urgente` | PHP rechaza la actualización y no modifica el registro. |
| Identificador `9999` | La aplicación informa que la tarea no existe. |
| Reinicio del servidor | Las tareas registradas permanecen en SQLite. |

## Ampliación implementada

**Búsqueda por título**

- Tipo: consulta.
- Clase: `App\Queries\ListarTareas`.
- Archivo principal: `src/Queries/ListarTareas.php`.
- Ruta: `?titulo=texto`.
- También puede combinarse con estado y prioridad.
- No modifica datos.

La búsqueda utiliza `LIKE` con parámetros preparados y escapa `%` y `_` para tratarlos como caracteres literales.

## Prueba automática

Ejecute la prueba desde la raíz del proyecto:

```powershell
herd php tests/smoke.php
```

Alternativamente, si el lanzador PHP de Herd no está disponible como comando global:

```powershell
& "$env:USERPROFILE\.config\herd\bin\php83.bat" tests/smoke.php
```

Resultado esperado:

```text
OK: crear, consultar, actualizar y eliminar.
```

La prueba crea una base temporal, verifica búsquedas, creación, consulta, actualización y eliminación, y luego elimina el archivo temporal.

Para comprobar la sintaxis de todos los archivos PHP:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    herd php -l $_.FullName
}
```

## Flujo de trabajo Git

```powershell
git switch main
git pull origin main
git switch -c feature/busqueda-titulo
git add .
git commit -m "Agrega búsqueda de tareas por título"
git push -u origin feature/busqueda-titulo
```

Luego se debe abrir un pull request hacia `main`, con una descripción, evidencia funcional y el resultado de las pruebas.

## CI/CD

El flujo `.github/workflows/php-ci.yml` se ejecuta ante `push` y pull request, y realiza:

1. Obtención del código.
2. Configuración de PHP 8.3.
3. Instalación de `pdo_sqlite`.
4. Validación de sintaxis de los archivos PHP.
5. Ejecución de `tests/smoke.php`.

## Conclusiones

1. Una clase Command modifica el estado mediante operaciones de creación, actualización o eliminación. Una clase Query solamente obtiene información mediante consultas `SELECT`.
2. El validador `TareaValidator` impide el ingreso de títulos cortos, prioridades o estados no permitidos y descripciones excesivas. También se aplican restricciones `CHECK` y `NOT NULL` en SQLite.
3. El flujo de rama y pull request permite revisar el código antes de combinarlo. Cuando se integra CI/CD, GitHub Actions puede ejecutar automáticamente las comprobaciones de sintaxis y la prueba CRUD antes de aprobar o desplegar el cambio.
