# CATASSR

Aplicativo local para PHP + MySQL/MariaDB + XAMPP. Primera etapa de administración y accesos.

## Nuevo: IMI de prueba

[Descargar actualización IMI](descargas/CATASSR_IMI_PRUEBA.zip) · [Guía de instalación y fórmulas](ACTUALIZAR_IMI.md) · [Plantilla Excel](plantillas/IMI_CATASSR_PRUEBA.xlsx)

Registro por establecimiento y mes con cuatro campos editables, saldos, CPM, pedidos sugeridos, borradores, envíos y seguimiento por microred. Las reglas son propuestas de prueba que deben validarse antes del uso operativo.

## Actualizar una instalación existente

Para la corrección de creación de establecimientos y medicamentos, reemplaza únicamente `CATASSR/index.php`. Consulta [ACTUALIZAR.md](ACTUALIZAR.md). No reinstales ni importes SQL.

## Descargar e instalar

1. En GitHub, pulsa **Code → Download ZIP**.
2. Descomprime el archivo. Dentro de `CATASSR-main`, copia la carpeta **CATASSR** a `C:\xampp\htdocs\`.
3. Inicia **Apache** y **MySQL** en XAMPP.
4. Abre **http://localhost/CATASSR/instalar.php**.
5. Introduce la contraseña que usarás para el administrador y pulsa **Instalar CATASSR**.
6. Ingresa en **http://localhost/CATASSR/** con el usuario **victor.infantes** y tu contraseña.

El administrador se crea con el nombre **VICTOR INFANTES**. No hace falta importar SQL: el instalador crea la base `catassr` y carga los catálogos. Tras instalar, elimina `instalar.php`, `datos.json` y `schema.sql` de la copia en htdocs.

Requiere PHP 8.1 o superior con PDO MySQL. `config.php` contiene los valores habituales de XAMPP local: host 127.0.0.1, puerto 3306, usuario root, contraseña vacía. Para una configuración privada crea `config.local.php`, con el mismo array y tus valores. Está excluido de Git.

## Incluye

- Creación de establecimientos y medicamentos/insumos exclusiva del administrador, validada también en el servidor.
- Cuatro roles: administrador, equipo técnico, cabecera de microred y encargada/o de establecimiento.
- Crear, editar, desactivar, eliminar y asignar usuarios paulatinamente.
- 196 establecimientos, 11 microredes y 13 medicamentos e insumos importados de los Excel proporcionados.
- Consulta limitada a la microred o establecimiento de la cuenta.
- Contraseñas con hash, consultas preparadas, tokens CSRF y bloqueo temporal por intentos fallidos.
- Diseño adaptable en azul y turquesa, inspirado en la referencia del usuario.

Las cuentas de los 196 responsables se crean conforme se asignan. No se generan contraseñas genéricas.

## Alcance

Esta entrega incluye administración, catálogos e IMI de prueba. El seguimiento de IMI muestra informes pendientes, en borrador y enviados. El panel principal conserva sus cifras de asignación administrativa. Consulta ACTUALIZAR_IMI.md para conocer los cálculos y límites de la prueba.

La contraseña del administrador se introduce durante la instalación; no está publicada en el repositorio.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: instalación, conteos, roles, aislamiento de ámbitos, CSRF, edición, desactivación, cambio de contraseña y bloqueo por intentos fallidos. Pendiente de prueba visual y de verificación directamente en Windows/XAMPP.

Más instrucciones en [CATASSR/LEEME.txt](CATASSR/LEEME.txt).
