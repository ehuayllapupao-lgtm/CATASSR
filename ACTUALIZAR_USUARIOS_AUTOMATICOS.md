# Usuarios generados automáticamente

## Actualizar CATASSR

1. Descarga **descargas/CATASSR_USUARIOS_AUTOMATICOS.zip** y descomprímelo.
2. Copia la carpeta **CATASSR** dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
3. Recarga la página e ingresa como administrador en **Usuarios y accesos**.

No requiere migración de base de datos, reinstalar ni modificar config.php. Conserva las cuentas, contraseñas, roles, asignaciones, IMI y fechas de vencimiento actuales. Esta actualización se aplica sobre la versión actual de CATASSR.

## Crear una cuenta

Completa por separado:

- **Nombres:** Rosangélica Laís
- **Apellido paterno:** Chávez
- **Apellido materno:** Marchán

El campo Usuario se completa como **RCHAVEZM**.

La regla es: primera letra del primer nombre + apellido paterno completo + primera letra del apellido materno, todo en mayúsculas y sin tildes, espacios ni signos. Ñ se convierte en N. En apellidos compuestos se escribe el apellido completo en su campo; por ejemplo, de la Cruz se convierte en DELACRUZ.

El campo Usuario es de solo lectura. La vista previa aparece al completar los tres campos; el servidor también genera y valida el identificador al guardar, incluso si JavaScript no está disponible. Un valor enviado manualmente para Usuario no sustituye al generado.

Si el usuario ya existe, se asigna un número al final: RCHAVEZM1, RCHAVEZM2, etc. El mensaje después de guardar muestra el usuario definitivo que debe utilizarse para ingresar. Los nombres de cuentas inactivas siguen reservados.

Luego completa la contraseña, el rol, la asignación correspondiente y el estado de la cuenta. La contraseña se establece como antes y no se genera automáticamente.

## Cuentas existentes

Conservan sus nombres de usuario actuales, incluido victor.infantes. Al editar una cuenta, su usuario aparece como solo lectura y no cambia al corregir el nombre, la contraseña, el rol o la asignación. Así se mantienen los accesos ya entregados.

El nombre completo se guarda en la estructura actual de la base. No es necesario dividir o modificar automáticamente nombres de las cuentas antiguas.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: ejemplo solicitado, nombres compuestos, tildes y Ñ, Unicode compuesto/descompuesto, usuarios repetidos, cuentas inactivas, inicio de sesión con el usuario generado, conservación de usuarios existentes, validación de apellidos, permisos y CSRF. La vista previa JavaScript se verificó con el mismo mapa de normalización del servidor. Pendiente de prueba visual directamente en Windows/XAMPP.

Los archivos no incluyen cuentas ni contraseñas de las pruebas.
