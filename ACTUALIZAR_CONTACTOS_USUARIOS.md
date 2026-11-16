# Teléfonos y correo de usuarios

## Actualizar CATASSR

1. Descarga **descargas/CATASSR_CONTACTOS_USUARIOS.zip** y descomprímelo.
2. Guarda una copia de tu carpeta CATASSR y de la base de datos.
3. Copia la carpeta **CATASSR** del ZIP dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
4. Ingresa como administrador.
5. Abre `http://localhost/CATASSR/actualizar_contactos.php` y pulsa **Habilitar teléfonos y correo**.
6. En **Usuarios y accesos**, crea una cuenta o pulsa **Editar**.

No reinstales CATASSR ni importes la base inicial. La actualización agrega tres columnas opcionales a usuarios: telefono_1, telefono_2 y correo. Conserva usuarios, contraseñas, roles, asignaciones, productos, establecimientos, IMI y periodos. Puede repetirse sin borrar los contactos existentes. Si una ejecución se interrumpe, se puede volver a ejecutar para agregar únicamente los campos que faltan.

## Datos de contacto

El formulario incorpora un apartado con:

- **Número telefónico 1**.
- **Número telefónico 2**.
- **Correo**.

Los tres son opcionales. Los usuarios existentes aparecen sin contacto hasta que se ingrese uno real. Para quitar un dato, vacía el campo y guarda.

Los teléfonos admiten entre 7 y 15 dígitos, con + al inicio, espacios, paréntesis y guiones; hasta 30 caracteres de longitud total. No incluyen extensiones alfabéticas. El correo debe tener un formato de correo electrónico válido y no superar 254 caracteres. Se permite que distintas cuentas compartan un contacto institucional.

Los contactos no se utilizan como usuario de inicio de sesión. Se mantienen la generación automática del usuario y la doble función de establecimiento/cabecera.

Solo el administrador puede crear y modificar estos datos desde Usuarios y accesos. Si todavía no se ejecutó la actualización, se ofrece el botón para habilitarlos antes de guardar usuarios.

## Ver ficha

Los teléfonos y el correo aparecen en **Establecimientos de salud → Ver ficha**, únicamente dentro del ámbito de consulta de la cuenta. Los datos ausentes se muestran como **Sin registrar**. La ventana y la página alternativa siguen verificando la sesión y el ámbito en el servidor; no exponen contraseñas ni hashes.

Antes de habilitar los campos, la ficha continúa funcionando y muestra los contactos como sin registrar. Las cuentas sin responsable de establecimiento pueden guardar su contacto desde Usuarios y accesos, aunque no tengan una ficha vinculada a un establecimiento.

## Verificación

Probado con PHP 8.4/MariaDB 11.8: migración repetible y conservación de cuentas y datos, alta y edición con contactos, formato inválido de teléfono/correo, campos opcionales, eliminación explícita de valores, conservación de contactos cuando un formulario antiguo omite los campos, ficha dentro/fuera del ámbito, usuarios y contraseñas conservados, permisos y CSRF. Pendiente de prueba visual directa en Windows/XAMPP.

Los archivos no incluyen datos de contacto, cuentas ni contraseñas de las pruebas.
