# Gestor de tareas en PHP

Aplicación web pequeña para practicar persistencia, operaciones CRUD, validación de datos y separación básica entre comandos y consultas.

## Requisitos

- Laravel Herd para Windows.
- PHP 8.1 o superior administrado por Herd.
- Extensiones `pdo_sqlite` y `mbstring` habilitadas en ese PHP.
- PowerShell, Git y un navegador web.

El proyecto utiliza PHP puro y no requiere el framework Laravel. Herd proporciona PHP, nginx y el dominio local `.test`. No necesita XAMPP, Composer, Node.js ni un servidor de base de datos independiente. No ejecute `composer install`: el proyecto no tiene dependencias de Composer.

## Preparación con Laravel Herd

1. Inicie la aplicación Laravel Herd y complete la configuración inicial.
2. En **Settings > PHP**, instale PHP 8.3 o superior y confirme que estén habilitadas las extensiones `pdo_sqlite` y `mbstring`. Seleccione esa versión como versión global desde el menú de Herd en la bandeja del sistema.
3. Cierre y vuelva a abrir PowerShell en la carpeta del proyecto. Compruebe la versión y las extensiones de PHP administrado por Herd:

```powershell
herd php -v
herd php -m | Select-String 'pdo_sqlite|mbstring'
```

El segundo comando debe mostrar tanto `pdo_sqlite` como `mbstring`.

4. Entre en la carpeta pública y registre el sitio en Herd:

```powershell
cd public
herd link gestor-tareas-s13
```

5. Después abra en el navegador:

```text
http://gestor-tareas-s13.test
```

La carpeta `public` se enlaza como raíz del sitio, mientras que `config`, `src` y `database` permanecen fuera del acceso web directo. La carpeta `database` y el archivo `tareas.sqlite` se crean automáticamente al abrir la aplicación.

Para retirar el enlace local al finalizar:

```powershell
herd unlink gestor-tareas-s13
```

También puede agregar la carpeta `public` desde la interfaz de Herd como un sitio existente y asignarle el nombre `gestor-tareas-s13`.

### Solución de problemas

- Si `herd php -v` muestra `No usable PHP version found`, instale una versión de PHP desde **Settings > PHP** y selecciónela en el menú de Herd. Después abra una terminal nueva.
- Si PowerShell dice que `herd` no se reconoce, confirme que `%USERPROFILE%\.config\herd\bin` esté en el `Path` de Windows y vuelva a abrir la terminal.
- Si falta `pdo_sqlite` o `mbstring`, habilite la extensión en la configuración de PHP de Herd y repita la comprobación.

## Funcionalidades

- Crear tareas.
- Listar y filtrar por estado o prioridad.
- Consultar el detalle.
- Actualizar tareas.
- Eliminar tareas.
- Validar título, descripción, prioridad y estado.
- Controlar identificadores inexistentes.
- Persistir los datos en SQLite.

## Estructura CQRS básica

```text
src/Commands   Operaciones que modifican datos
src/Queries    Operaciones que solamente consultan datos
```

La práctica no implementa una arquitectura CQRS distribuida. Se utiliza una sola base de datos y se separan responsabilidades para reconocer comandos y consultas.

## Rutas y operaciones

| Operación | Ruta | Componente |
| --- | --- | --- |
| Listar y filtrar | `?` | `src/Queries/ListarTareas` |
| Crear | `?accion=crear` | `src/Commands/CrearTarea` |
| Consultar detalle | `?accion=detalle&id=1` | `src/Queries/ObtenerTarea` |
| Actualizar | `?accion=editar&id=1` | `src/Commands/ActualizarTarea` |
| Eliminar | `?accion=eliminar&id=1` | `src/Commands/EliminarTarea` |

Las operaciones de escritura se envían mediante formularios. La aplicación valida los datos en PHP y usa restricciones de SQLite para proteger la persistencia.

## Prueba automática

Ejecute desde la raíz del proyecto:

```powershell
herd php tests/smoke.php
```

La salida esperada es `OK: crear, consultar, actualizar y eliminar.` La prueba usa una base temporal y la elimina al finalizar.

## Flujo Git recomendado

Cada equipo debe crear su propio repositorio a partir de esta plantilla.

```bash
git checkout -b feature/nombre-corto
git add .
git commit -m "Describe el cambio"
git push -u origin feature/nombre-corto
```

Después abra un pull request hacia `main` en el repositorio del equipo. No se recomienda utilizar una sola rama compartida por todos los grupos.

## Desafíos por equipo

Cada equipo debe elegir una ampliación:

1. Fecha límite.
2. Categorías.
3. Búsqueda por título.
4. Eliminación lógica.
5. Resumen de tareas por estado.

## Preparación para CI/CD

El archivo `.github/workflows/php-ci.yml` ejecuta validación de sintaxis y la prueba automática en cada `push` o pull request. Más adelante puede añadirse una etapa de despliegue sin cambiar el flujo de ramas.

