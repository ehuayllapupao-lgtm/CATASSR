# Estrategia fija, varias áreas y fecha de conclusión

## Actualizar

1. Descarga **descargas/CATASSR_CONFIG_DISTRIBUCIONES.zip** y descomprímelo.
2. Conserva una copia de CATASSR y de la base de datos.
3. Copia la carpeta **CATASSR** del ZIP dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
4. Ingresa como administrador.
5. Abre `http://localhost/CATASSR/actualizar_distribuciones.php` y pulsa **Habilitar Distribuciones** para habilitar la selección de varias áreas.
6. Recarga con Ctrl+F5.

La actualización agrega la tabla que relaciona cada programación con sus áreas. Conserva los IMI, usuarios, inventario, movimientos, distribuciones y fechas existentes. No reinstales CATASSR ni importes la base inicial. Se puede repetir sin perder las áreas ya elegidas.

## Nueva programación

- **Estrategia sanitaria:** siempre **PROGRAMA 002 SALUD MATERNO NEONATAL**. Se muestra como solo lectura y se fija también en el servidor; el navegador no puede sustituirla por otra.
- **Área:** marca una o varias de estas opciones: **PLANIFICACION FAMILIAR**, **INSUMOS**, **INSTRUMENTOS MEDICOS** y **MEDICAMENTOS**. Debe elegirse al menos una.
- **Fecha actual:** aparece automáticamente en hora de Lima, encima de Proyección de meses y como solo lectura.
- **Fecha de conclusión:** durante el borrador figura pendiente en el resumen de la programación. Se registra automáticamente con la fecha actual de Lima al confirmar el cierre de toda la programación. No se puede anticipar ni alterar enviando una fecha desde el navegador.
- **Botón de inicio:** ahora dice **Iniciar distribución**. Inicia la programación en borrador; no descuenta stock ni registra una fecha de conclusión.

Se mantienen el año, el número de distribución, la proyección de 3 a 6 meses y el análisis por medicamento y establecimiento.

Las áreas elegidas se muestran juntas en el resumen de la programación y en su exportación. No dividen ni duplican el inventario ni las cantidades: son categorías de una misma programación.

## Datos anteriores

Las distribuciones ya guardadas conservan la estrategia, área y fecha con las que fueron registradas. La actualización asocia su área original a la nueva tabla para mantener el historial. La estrategia fija y las cuatro áreas disponibles se aplican al iniciar nuevas programaciones.

El botón **Guardar borrador del producto** conserva su función. Solo se cambió el nombre del botón inicial de la creación.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: configuración fija, rechazo de áreas no permitidas o vacías, selección de varias áreas, guardado y exportación, fecha real de Lima al cierre, fecha no manipulable desde el formulario, migración repetible, conservación de áreas históricas y permisos. También se comprobó que, ante un error del formulario, se conserven año, número, proyección y áreas elegidas.

El paquete no incluye datos ni contraseñas de las pruebas. Pendiente de comprobación visual directa en Windows/XAMPP.
