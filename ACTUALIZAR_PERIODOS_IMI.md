# Control de periodos de ingreso IMI

## Instalar sobre CATASSR existente

1. Descarga **descargas/CATASSR_PERIODOS_IMI.zip** y descomprímelo.
2. Conserva una copia de tu carpeta CATASSR y de la base de datos.
3. Copia la carpeta **CATASSR** del ZIP dentro de `C:\xampp\htdocs\`, reemplazando los archivos existentes.
4. Inicia Apache y MySQL e ingresa como administrador.
5. Abre `http://localhost/CATASSR/actualizar_imi.php` y pulsa **Habilitar módulo IMI**. El proceso es idempotente: crea las tablas que faltan sin borrar los datos.
6. En el menú, abre **Periodos IMI**. Selecciona el mes reportado y pulsa **Abrir / actualizar plazo**.

El paquete es acumulativo para el módulo IMI e incluye los archivos necesarios de la versión anterior. No contiene config.php, config.local.php, instalar.php ni datos de prueba. No reinstales ni importes la base inicial. Esta actualización añade la tabla imi_periodos y conserva los informes, medicamentos, establecimientos, usuarios y asignaciones.

## Abrir o programar el ingreso

Solo los administradores pueden gestionar los periodos. Cada mes reportado tiene un control global para todos los establecimientos.

- Elige el mes del informe (por ejemplo, octubre), que puede ser distinto del mes de ingreso (por ejemplo, noviembre).
- Deja inicio y fin vacíos para abrir inmediatamente hasta que lo cierres manualmente.
- Si defines inicio y/o fin, la ventana se respeta automáticamente en cada solicitud, con hora de Lima (America/Lima). No requiere tareas programadas ni configurar la zona horaria de MySQL.
- Antes del inicio y después del fin no se permite guardar, enviar, registrar apertura ni reabrir informes.
- Los meses sin habilitación quedan cerrados por defecto, incluidos los meses anteriores a esta actualización. Los datos ya guardados se conservan.

## Cerrar

Pulsa **Cerrar ingreso ahora**. El cierre es inmediato, incluso si había un plazo programado. Se bloquea el ingreso para todos, incluido el administrador; para corregir se debe abrir primero el periodo.

El control está en el servidor y dentro de las transacciones de guardado. Una página que quedó abierta antes del cierre no puede guardar después del cierre. También se vuelve a comprobar el plazo antes de confirmar los cambios.

## Qué se puede consultar después del cierre

- Los IMI enviados conservan la consulta, impresión y exportación según el ámbito de cada usuario.
- Los borradores no se eliminan ni se convierten en enviados. Para encargados, cabeceras y equipo técnico, el periodo cerrado muestra únicamente el contenido de informes enviados; un borrador o informe pendiente se indica como sin IMI enviado.
- El administrador puede revisar los borradores conservados, sin modificarlos mientras el periodo esté cerrado.
- La cabecera sigue limitada a su microred; el encargado, a su establecimiento.

## Reabrir para corregir

Abre el periodo nuevamente. Los borradores se pueden retomar conservando sus datos. Un informe ya enviado continúa bloqueado hasta que el administrador pulse **Reabrir último informe** en IMI. Se mantiene la regla de integridad anterior: solo se modifica el último periodo del establecimiento, sin alterar los cierres de meses ya encadenados.

Abrir un periodo no habilita automáticamente la edición de informes enviados ni elimina las restricciones de acceso.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: migración, permisos por rol, ventanas futuras/vencidas, cierre manual, rangos inválidos, conservación y ocultamiento de borradores, consulta/exportación de enviados, rechazo de páginas antiguas y protección CSRF. Los archivos se entregan sin datos de las pruebas. Pendiente de verificación visual directamente en Windows/XAMPP.
