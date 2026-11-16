# Una cuenta como encargada de establecimiento y cabecera de microred

## Actualizar

1. Descarga **descargas/CATASSR_DOBLE_FUNCION.zip** y descomprímelo.
2. Copia la carpeta **CATASSR** dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
3. Recarga con Ctrl+F5 e ingresa como administrador en **Usuarios y accesos**.

Copia todos los archivos del paquete juntos: incluyen el formulario, la etiqueta del perfil y la comprobación de permisos del IMI. No requiere reinstalar, ejecutar actualizadores ni modificar la base de datos. Se utilizan las asignaciones existentes de establecimiento y microred.

## Asignar ambas funciones

1. Crea una cuenta o pulsa **Editar** en la cuenta de la persona.
2. Selecciona **Encargada/o de establecimiento** en Tipo de acceso.
3. Selecciona su **Establecimiento**.
4. Marca **También es cabecera de microred**.
5. Selecciona su **Microred** y guarda.

Una sola cuenta, usuario y contraseña sirven para ambas funciones. No necesitas crear otra cuenta para la misma persona. El usuario automático y las contraseñas existentes se conservan como antes. Al editar, deja la contraseña vacía para conservarla.

## Qué puede hacer

- Registrar, guardar borradores y enviar el IMI de su establecimiento asignado, cuando el periodo está abierto.
- Consultar y exportar los IMI de los establecimientos de la microred asignada, según los mismos controles de periodo y envío vigentes.
- Consultar el resumen de microred y el avance de los establecimientos de su ámbito.
- No puede modificar los IMI de otros establecimientos, aunque pertenezcan a la microred que supervisa.
- No puede administrar usuarios, medicamentos, establecimientos o periodos.

Si la microred supervisada es distinta de la de su establecimiento, la cuenta conserva el acceso a su propio establecimiento y consulta además la microred asignada. No obtiene acceso a otros establecimientos de la microred de origen por ese solo hecho.

El formulario valida ambas asignaciones. Se mantiene el límite de una cuenta encargada por establecimiento. Un establecimiento ya asignado a otra cuenta no se reasigna automáticamente.

## Retirar la función de cabecera

Edita la cuenta, desmarca **También es cabecera de microred** y guarda. La cuenta conservará su establecimiento y perderá la consulta adicional de la microred, incluso si ya tenía una sesión abierta. Los informes y la cuenta no se eliminan.

Para una persona que solo supervisa una microred, sigue utilizando **Cabecera de microred** como tipo de acceso. Las cuentas existentes no obtienen permisos adicionales automáticamente.

## Verificación

Probado con PHP 8.4/MariaDB 11.8: alta y edición de doble función, asignaciones inválidas, consulta/exportación por ámbito, escritura limitada al establecimiento asignado, retiro inmediato del permiso, microred distinta, rechazo de solicitudes directas a otros establecimientos, cierre de periodos y conservación de los filtros y resúmenes. JavaScript comprobado para la vista previa y la habilitación de asignaciones según el rol. Pendiente de prueba visual directamente en Windows/XAMPP.

Los archivos no incluyen cuentas, contraseñas ni registros de las pruebas.
