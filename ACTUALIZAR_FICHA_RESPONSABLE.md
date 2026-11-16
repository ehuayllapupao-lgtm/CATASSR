# Autocompletado y ficha del responsable

## Actualizar en XAMPP

1. Descarga **descargas/CATASSR_BUSQUEDA_FICHA.zip** y descomprímelo.
2. Copia la carpeta **CATASSR** dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
3. Recarga **Establecimientos de salud** con Ctrl+F5.

El paquete actualiza index.php y style.css y agrega establecimientos.js y ficha_responsable.php. No requiere reinstalar, ejecutar actualizadores ni modificar la base de datos. Mantiene las funciones anteriores.

## Buscar

Escribe el nombre, código o distrito. Se muestran sugerencias y la tabla se filtra mientras escribes, sin tener que pulsar Filtrar. Puedes seleccionar una sugerencia para completar el campo. La microred seleccionada limita tanto los resultados como las sugerencias.

La búsqueda reconoce tildes habituales del español y conserva los códigos con ceros iniciales. El contador muestra cuántos establecimientos coinciden. Sin JavaScript, el botón Filtrar sigue funcionando.

## Ver ficha

La columna **Ver ficha** aparece al lado de **Responsable**. Pulsa el botón para abrir una ventana con los datos actuales de esa cuenta y su asignación:

- Nombre completo y usuario.
- Funciones: encargado de establecimiento y, si corresponde, cabecera de microred.
- Estado activo o inactivo.
- Establecimiento, código, microred, distrito y categoría.
- Microred que supervisa, cuando tenga esa función.

Puedes cerrar con Cerrar, Escape o al pulsar fuera de la ventana. Si el navegador no admite el diálogo o JavaScript está desactivado, el enlace abre una página con la ficha.

Donde no hay responsable asignado se muestra **Sin responsable**, sin abrir fichas vacías. Una cuenta inactiva continúa apareciendo como responsable asignado y su ficha indica que está inactiva.

La ficha usa exclusivamente los datos existentes. No incluye DNI, teléfono, correo o dirección porque esos campos todavía no están registrados en CATASSR. No inventa información ni modifica las cuentas.

## Permisos

Sugerencias, tabla y fichas se limitan al ámbito de la cuenta. El administrador y el equipo técnico consultan el ámbito general; las cabeceras, su microred; los encargados, su establecimiento; y las cuentas con doble función, su establecimiento más la microred asignada.

La ficha requiere sesión activa y vuelve a verificar el ámbito en el servidor. No expone contraseñas, hashes, intentos de acceso ni datos de usuarios ajenos al ámbito. La respuesta no se almacena en caché.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: autenticación, ámbitos simples y dobles, fichas JSON/HTML, registros sin responsable, sesión obligatoria, búsqueda y filtrado sin JavaScript. Verificado JavaScript para sugerencias y filtrado en vivo por nombre/código/distrito, tildes, microred y ausencia de resultados. No se ha realizado una prueba visual de la ventana en Windows/XAMPP.

Los archivos no contienen datos ni contraseñas de las pruebas.
