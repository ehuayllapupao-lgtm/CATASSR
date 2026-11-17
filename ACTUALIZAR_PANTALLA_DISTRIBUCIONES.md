# Simplificar la pantalla de Distribuciones

## Instalar

1. Descarga **descargas/CATASSR_LIMPIEZA_DISTRIBUCIONES.zip** y descomprímelo.
2. Copia **CATASSR/distribuciones.php** a `C:\xampp\htdocs\CATASSR\distribuciones.php` y acepta reemplazarlo.
3. Recarga Distribuciones con Ctrl+F5.

No requiere reinstalar ni actualizar la base de datos.

## Cambios visibles

- **Almacén general** se traslada al menú lateral, inmediatamente debajo de Distribuciones.
- Se retira el acceso **Estrategias y áreas** de esa navegación.
- Se elimina el bloque desplegable **Seleccionar distribución**.
- Se conserva la creación de programaciones y el análisis por producto y establecimiento.
- Los borradores y distribuciones anteriores se pueden abrir desde **Historial de programaciones guardadas**, una sección plegada que respeta los permisos de cada cuenta.

Los campos Estrategia sanitaria y Área de una programación se mantienen; lo retirado es el acceso a su apartado de gestión. No se borran los catálogos ni los datos anteriores.

## Alcance de este cambio

Es una simplificación de la pantalla principal. No elimina tablas, inventario, movimientos ni distribuciones, y no retira la validación o el descuento de stock al cerrar. El almacén se abre desde el menú lateral, con sus permisos originales. Los accesos internos anteriores a los catálogos se conservan para compatibilidad.

Las fechas, proyección de 3 a 6 meses, borradores, cierre, controles de stock y permisos mantienen su comportamiento.

Verificado: sintaxis PHP y respuesta de la pantalla para administrador y encargado; ausencia de los accesos/selector retirados, acceso a borradores desde historial y ocultamiento de borradores a cuentas de consulta. No se modificaron datos durante esta comprobación.
