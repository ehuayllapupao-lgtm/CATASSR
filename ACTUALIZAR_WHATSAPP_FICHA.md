# WhatsApp desde la ficha del responsable

## Actualizar CATASSR

1. Descarga **descargas/CATASSR_WHATSAPP_FICHA.zip** y descomprímelo.
2. Copia la carpeta **CATASSR** dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
3. Recarga **Establecimientos de salud** con Ctrl+F5 y abre **Ver ficha**.

No requiere reinstalar ni actualizar la base de datos. Se aplica sobre la versión con teléfonos y correo habilitados. El paquete actualiza la ficha, su JavaScript, los estilos y la función que genera los enlaces.

## Usar

Junto a cada teléfono con formato utilizable aparece un icono verde de WhatsApp. Al pulsarlo se abre el chat en una pestaña nueva para que el usuario pueda iniciar la conversación en WhatsApp Web o continuar en la aplicación, según su dispositivo.

El enlace no contiene un mensaje precargado ni envía mensajes automáticamente. La función también está disponible en la página alternativa de ficha cuando no se utiliza la ventana emergente.

Los contactos se editan desde **Usuarios y accesos**, por el administrador. La ficha mantiene sus permisos de consulta por ámbito.

## Formato de los números

- Celular peruano de nueve dígitos que comienza con 9: se añade el código de país 51.
- Número con + y código de país: se usa como internacional, quitando espacios, paréntesis y guiones.
- También se reconoce el prefijo internacional 00 y los números peruanos que ya incluyen 51.
- Se admiten los números fijos peruanos con código de área: ocho dígitos sin el cero inicial de larga distancia, o con ese cero inicial. Un fijo solo puede recibir el chat si está asociado a WhatsApp.
- Un número local sin código de área o país no se adivina: debe completarse desde Usuarios y accesos.
- Si está vacío, figura como Sin registrar y no se muestra el icono.

Los enlaces tienen el formato https://wa.me/ seguido solo de dígitos del número internacional. La disponibilidad de la conversación depende de que el número tenga una cuenta de WhatsApp.

## Verificación

Probado con PHP 8.4/MariaDB 11.8 y JavaScript: dos teléfonos, formato peruano e internacional, prefijo +51/0051, números vacíos o ambiguos, HTML alternativo, iconos en el modal, enlaces en pestaña nueva, rechazo de enlaces no permitidos y control de acceso. No se enviaron mensajes ni se abrieron conversaciones reales durante las pruebas. Pendiente de verificación visual en Windows/XAMPP.

Los archivos no incluyen teléfonos, cuentas ni contraseñas de las pruebas.
