# **GUÍA DE PRÁCTICA N.º 13**

**PATRONES DE PERSISTENCIA Y GESTIÓN DE DATOS**

Construcción guiada de un gestor de tareas con PHP SQLite y Git

| DATOS ACADÉMICOS | INFORMACIÓN |
| ----- | ----- |
| Unidad didáctica | Gestión de aplicaciones web |
| Periodo académico / semestre | 2026-II / VI semestre |
| Semana | 13 |
| Fechas | 11 y 12/11/2026 (diurno) \- 09 y 11/11/2026 (nocturno) |
| Título de la sesión | Patrones de persistencia y gestión de datos |
| Indicador de logro | Aplica operaciones CRUD y criterios del patrón CQRS de acuerdo con la arquitectura y necesidades de la aplicación web. |
| Docente | Ing. Erick Donniex Moscoso Bravo |
| Equipo / integrantes | \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_ |

# **I. PROPÓSITO DE LA PRÁCTICA**

Construir y comprobar una aplicación web pequeña sin requerir un software previo. A partir del Gestor de tareas proporcionado por el docente, los estudiantes ejecutarán operaciones CRUD con persistencia SQLite, reconocerán la separación entre comandos y consultas, aplicarán una ampliación y registrarán el trabajo en Git mediante una rama y un pull request.

# **II. PRODUCTO DE LA PRÁCTICA**

* Gestor de tareas ejecutado localmente con operaciones CRUD verificadas.  
* Ampliación funcional desarrollada por el equipo sobre una rama feature.  
* Identificación de comandos y consultas en la estructura del código PHP.  
* Repositorio del equipo con commits, pull request y ejecución correcta de la prueba automática.

# **III. EQUIPOS Y MATERIALES**

* Proyecto base S13 Gestor de Tareas PHP entregado por el docente.  
* Laravel Herd para Windows con PHP 8.1 o superior y las extensiones PDO SQLite y mbstring habilitadas.  
* Editor de código, navegador, PowerShell, Git y Laravel Herd.  
* Cuenta o repositorio Git del equipo y esta guía editable para registrar evidencias.

# **IV. REFERENCIA TÉCNICA BREVE**

Persistir significa conservar los datos después de terminar una solicitud o reiniciar el servidor. CRUD agrupa las operaciones crear, consultar, actualizar y eliminar. CQRS separa las operaciones que modifican el estado de las que solamente consultan. En esta práctica se usa una sola base SQLite y una separación sencilla por carpetas para facilitar el aprendizaje.

| CONCEPTO | PREGUNTA PRÁCTICA | EJEMPLO |
| ----- | ----- | ----- |
| Create | ¿Cómo se registra una tarea válida? | Formulario Nueva tarea invoca CrearTarea y guarda en SQLite. |
| Read | ¿Cómo se consultan las tareas? | ListarTareas y ObtenerTarea no modifican datos. |
| Update | ¿Cómo se modifica una tarea? | ActualizarTarea valida antes de ejecutar UPDATE. |
| Delete | ¿Cómo se elimina una tarea? | EliminarTarea controla que el id exista. |
| Comando | ¿La operación cambia el estado? | Crear, actualizar y eliminar. |
| Consulta | ¿La operación solo obtiene datos? | Listar, filtrar y consultar detalle. |

# **V. PROCEDIMIENTO**

## **Actividad 1\. Preparar y ejecutar el proyecto base**

Descompriman o clonen el proyecto entregado. Inicien Laravel Herd, verifiquen PHP, enlacen la carpeta public como sitio local y abran la aplicación con el dominio .test.

**Ejemplo:** desde la carpeta del proyecto se verifica herd php \-v; después se ingresa a public y se ejecuta herd link gestor-tareas-s13. 

La aplicación se abre en http\://gestor-tareas-s13.test y la base database/tareas.sqlite se crea automáticamente.

| CAMPO | RESPUESTA DEL EQUIPO |
| ----- | ----- |
| Versión PHP en Herd | PHP 8.3.36 mediante Herd |
| Laravel Herd activo | Sí ☒   No ☐ |
| Dominio local Herd | http\://gestor-tareas-s13.test |
| Repositorio del equipo | https://github.com/maxcz6/S13_Gestor_Tareas |
| Rama de trabajo | main (pendiente crear feature/busqueda-titulo) |
| Sitio enlazado en Herd | Sí ☒   No ☐ |
| Evidencia de ejecución | Evidencias 01 a 08 en la carpeta evidencias/ |

## **Actividad 2\. Seguir la demostración**

**Caso común:** Gestor de tareas. El docente explica la conexión PDO, la tabla SQLite, la validación y una operación de escritura y otra de lectura. Después muestra cómo las rutas de la interfaz invocan comandos o consultas.

| OPERACIÓN | RUTA | ENTRADA | RESULTADO ESPERADO |
| ----- | ----- | ----- | ----- |
| Crear | ?accion=crear | título, descripción, prioridad, estado | Inserta la tarea y muestra su detalle. |
| Listar | ? | filtros opcionales | Muestra la colección persistida. |
| Detalle | ?accion=detalle\&id=1 | id | Muestra una tarea o controla el id inexistente. |
| Actualizar | ?accion=editar\&id=1 | campos válidos | Guarda cambios y actualiza la fecha. |
| Eliminar | ?accion=eliminar\&id=1 | id y token | Elimina y vuelve al listado. |

**Ejemplo de ejecución**

cd public   y luego   herd link gestor-tareas-s13  
Abrir http\://gestor-tareas-s13.test y registrar la tarea Preparar evidencia CRUD  
Resultado esperado: la tarea aparece en la lista y permanece al reiniciar el servidor.  
**Ejemplo de validación**

Intentar guardar una tarea con título A  
El navegador envía el formulario al backend PHP.  
Resultado esperado: el backend rechaza el dato y no crea un registro en SQLite.

## **Separación básica con CQRS**

Las clases de src/Commands crean, actualizan o eliminan datos. Las clases de src/Queries solo listan u obtienen tareas. Esta separación es suficiente para reconocer CQRS en la sesión; no se usarán dos bases de datos ni bibliotecas adicionales.

| ENTRADA HTTP | TIPO | RESPONSABLE | PERSISTENCIA |
| ----- | ----- | ----- | ----- |
| Crear / editar / eliminar | Comando | src/Commands | Modifica SQLite mediante INSERT, UPDATE o DELETE. |
| Listar / filtrar / detalle | Consulta | src/Queries | Ejecuta SELECT sin modificar el estado. |

## **Actividad 3\. Ejecutar y explicar el CRUD**

Realicen las cinco operaciones sobre el Gestor de tareas. Para cada una, identifiquen la ruta utilizada, la clase que se ejecuta, el cambio observado en SQLite y la evidencia. No basta con tomar capturas: expliquen qué comando o consulta produjo el resultado.

| N.º | PRUEBA | DATOS UTILIZADOS | CÓDIGO Y RESULTADO | EVIDENCIA | CUMPLE |
| ----- | ----- | ----- | ----- | ----- | ----- |
| 1 | Crear una tarea válida | `titulo=CRUD Evidencia 2026`, `descripcion=Tarea creada para verificar persistencia y operaciones CRUD`, `prioridad=alta`, `estado=pendiente` | Ruta `?accion=crear` con `POST`. Ejecutó `CrearTarea`; `INSERT` creó el ID 5 y SQLite mostró el detalle creado. | [Formulario](./evidencias/01_crear_formulario.png) y [resultado](./evidencias/01_crear_resultado.png) | ☒ Sí |
| 2 | Listar y consultar detalle | `titulo=CRUD Evidencia 2026`, ID 5 | Ruta `?titulo=CRUD Evidencia 2026` ejecutó `ListarTareas`; la ruta `?accion=detalle&id=5` ejecutó `ObtenerTarea` con `SELECT`. No se modificaron datos. | [Listado](./evidencias/02_listar.png) y [detalle](./evidencias/02_detalle.png) | ☒ Sí |
| 3 | Actualizar estado o prioridad | ID 5; `prioridad=media`, `estado=completada` | Ruta `?accion=editar&id=5` con `POST`. Ejecutó `ActualizarTarea`; `UPDATE` cambió prioridad y estado y renovó `fecha_actualizacion`. | [Formulario](./evidencias/03_actualizar_formulario.png) y [resultado](./evidencias/03_actualizar_resultado.png) | ☒ Sí |
| 4 | Filtrar el listado | `titulo=CRUD Evidencia`, `estado=completada`, `prioridad=media` | Ruta `?titulo=CRUD+Evidencia&estado=completada&prioridad=media`. `ListarTareas` ejecutó un `SELECT` combinado y devolvió exactamente 1 tarjeta correspondiente al ID 5. | [Entrada](./evidencias/04_filtrar_entrada.png) y [resultado](./evidencias/04_filtrar_resultado.png) | ☒ Sí |
| 5 | Eliminar y consultar el id | ID 5 y token CSRF válido | Ruta `?accion=eliminar&id=5` ejecutó `EliminarTarea`; `DELETE` eliminó el registro y mostró `Operación completada: eliminada`. Al consultar `?accion=detalle&id=5`, `ObtenerTarea` devolvió `null` y respondió `La tarea solicitada no existe.` | [Confirmación](./evidencias/05_eliminar_confirmacion.png), [resultado](./evidencias/05_eliminar_resultado.png) y [ID inexistente](./evidencias/05_consulta_id_inexistente.png) | ☒ Sí |

## **Actividad 4\. Desarrollar una ampliación en una rama**

Cada equipo elegirá una ampliación: fecha límite, categorías, búsqueda por título, eliminación lógica o resumen por estado. Cree una rama con el formato feature/nombre-corto, implemente el cambio y añada al menos una prueba manual documentada.

| OPERACIÓN | COMANDO O CONSULTA | ARCHIVO / CLASE / FUNCIÓN | ¿MODIFICA ESTADO? | EVIDENCIA |
| ----- | ----- | ----- | ----- | ----- |
| Elegir ampliación | Consulta | [`App\Queries\ListarTareas`](./src/Queries/ListarTareas.php) con filtro `titulo` | No | Búsqueda por título registrada |
| Crear rama | Git | `feature/busqueda-titulo` | No | [Rama publicada en GitHub](https://github.com/maxcz6/S13_Gestor_Tareas/tree/feature/busqueda-titulo) |
| Implementar | Código | [`src/Queries/ListarTareas.php`](./src/Queries/ListarTareas.php) y [`public/index.php`](./public/index.php) | No: filtra sin modificar datos | Prueba manual: filtrado por título, estado y prioridad |
| Confirmar cambios | Git | commit `44a909a` | No | [Commit confirmado en GitHub](https://github.com/maxcz6/S13_Gestor_Tareas/commit/44a909a) |
| Abrir pull request | Git | Repositorio del equipo | No | [Crear pull request hacia main](https://github.com/maxcz6/S13_Gestor_Tareas/pull/new/feature/busqueda-titulo) |

## **Actividad 5\. Verificar validación y consistencia**

Ejecuten casos válidos e inválidos. Las reglas deben aplicarse en PHP y las restricciones principales deben estar respaldadas por SQLite. Comprueben título corto, valor no permitido, identificador inexistente, permanencia de datos y la prueba herd php tests/smoke.php.

| CASO | EJEMPLO | RESPUESTA ESPERADA | RESULTADO REAL | EVIDENCIA |
| ----- | ----- | ----- | ----- | ----- |
| Título demasiado corto | A | Rechaza y no inserta. | El navegador mostró que el título debe tener al menos 5 caracteres y evitó el envío. | [Evidencia](./evidencias/06_titulo_corto.png) |
| Prioridad no permitida | urgente | Rechaza y no actualiza. | PHP respondió `{"prioridad":"Seleccione una prioridad permitida."}` y no ejecutó el `INSERT`. | [Entrada](./evidencias/07_prioridad_invalida_entrada.png) y [resultado](./evidencias/07_prioridad_invalida_resultado.png) |
| Identificador inexistente | id 9999; probado también id 5 eliminado | Informa que no existe. | HTTP mostró `La tarea solicitada no existe.` y volvió al listado. | [Evidencia](./evidencias/05_consulta_id_inexistente.png) |
| Persistencia | recarga de detalle del ID 4 | La tarea permanece en SQLite. | Después de varias operaciones y recarga, el detalle ID 4 mostró sus datos originales y sus fechas. | [Evidencia](./evidencias/08_persistencia.png) |
| Prueba automática | herd php tests/smoke.php | Muestra OK sin errores. | PHP 8.3.36 mostró `OK: crear, consultar, actualizar y eliminar.` | Consola: prueba ejecutada con PHP Herd |

## **Actividad 6\. Registrar el trabajo con Git y preparar CI**

Cada equipo trabajará en su propio repositorio creado desde la plantilla. Los integrantes subirán la rama feature y abrirán un pull request hacia main. Revisen que la acción PHP CI valide la sintaxis y ejecute tests/smoke.php antes de combinar el cambio.

| CRITERIO | CUMPLE | AJUSTAR | OBSERVACIÓN |
| ----- | :---: | :---: | ----- |
| El repositorio pertenece al equipo, no a toda el aula. | ☒ | ☐ | Repositorio: https://github.com/maxcz6/S13_Gestor_Tareas |
| La ampliación se desarrolló en una rama feature. | ☐ | ☒ | La ampliación está implementada; falta crear y subir la rama feature/busqueda-titulo. |
| Los commits describen cambios concretos. | ☐ | ☒ | Pendiente confirmar el commit final con la implementación y evidencias. |
| El pull request explica qué cambió y cómo se probó. | ☐ | ☒ | Pendiente crear el pull request hacia main. |
| PHP CI o la prueba local termina correctamente. | ☒ | ☐ | PHP CI está configurado en `.github/workflows/php-ci.yml`; la prueba local terminó con OK. |

# **VI. CONTROL Y VERIFICACIÓN**

☒ El Gestor de tareas se ejecuta con Laravel Herd y un dominio local .test.

☒ Crear, listar, consultar, actualizar y eliminar producen resultados verificables.

☒ Los datos permanecen en SQLite después de una nueva consulta o reinicio.

☒ El equipo distingue las clases de comandos y consultas.

☒ El backend rechaza datos inválidos y controla identificadores inexistentes.

☒ La ampliación funciona. Falta formalizar la rama feature.

☐ El pull request contiene una descripción y evidencias del cambio.

☒ La validación automática PHP CI está configurada y la prueba local termina correctamente. Falta confirmar su ejecución en GitHub.

# **VII. CONCLUSIONES**

1\. ¿Qué diferencia observaron entre una clase Command y una clase Query?

Una clase Command modifica el estado y ejecuta INSERT, UPDATE o DELETE. Una clase Query solo consulta la base con SELECT y devuelve datos sin cambiarlos.

2\. ¿Qué regla evitó almacenar un dato inválido en SQLite?

La validación del título, entre 5 y 100 caracteres, impide datos demasiado cortos. PHP rechaza prioridades y estados no permitidos y SQLite respalda estas reglas mediante restricciones CHECK.

3\. ¿Cómo ayudará el flujo de rama y pull request cuando se incorpore CI/CD?

La rama aísla la ampliación y el pull request permite revisar exactamente qué cambió. GitHub Actions puede ejecutar PHP CI antes de combinar el cambio y evitar integrar código que no pase la sintaxis o el CRUD.