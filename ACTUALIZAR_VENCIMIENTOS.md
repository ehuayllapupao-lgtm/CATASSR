# Fechas de vencimiento — Medicamentos e insumos

## Actualizar una instalación existente

1. Descarga **descargas/CATASSR_VENCIMIENTOS.zip** y descomprímelo.
2. Conserva una copia de tu carpeta CATASSR y de la base de datos.
3. Copia la carpeta **CATASSR** del ZIP dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
4. Inicia Apache y MySQL e ingresa como administrador.
5. Abre `http://localhost/CATASSR/actualizar_vencimientos.php` y pulsa **Habilitar fechas de vencimiento**.
6. Ve a **Medicamentos e insumos** para registrar o editar las fechas.

La actualización agrega una columna DATE opcional a productos. Puede ejecutarse nuevamente sin borrar fechas existentes. No incluye archivos de conexión ni reinstala el sistema. Conserva los usuarios, productos, establecimientos, informes y periodos de ingreso.

## Usar el campo

- Al crear un medicamento o insumo, el administrador puede completar **Fecha de vencimiento**.
- Para un producto existente, pulsa **Editar vencimiento**, elige la fecha y pulsa **Guardar fecha**.
- Si aún no tienes la fecha, déjala vacía. Se muestra **Sin registrar**.
- Para quitar una fecha ingresada por error, vacía el campo y guarda. El producto se conserva.
- Los demás roles pueden consultar las fechas y estados, pero no modificarlos ni ejecutar la actualización.

## Estados automáticos

Se calculan con la fecha actual de Lima (America/Lima):

- **Vencido:** fecha anterior a hoy.
- **Vence hoy:** la fecha coincide con el día actual.
- **Próximo a vencer (30 días):** fecha posterior a hoy y dentro de los próximos 30 días, inclusive.
- **Vigente:** más de 30 días.
- **Sin registrar:** no tiene fecha ingresada.

El umbral de 30 días es una ayuda visual de esta versión. El sistema acepta fechas pasadas para reflejar los productos que ya están vencidos.

## Alcance

Se registra una fecha por producto del catálogo compartido. Esta versión no separa lotes ni existencias con vencimientos diferentes por establecimiento. Si un producto tiene distintos lotes, este campo no reemplaza el control de cada lote.

Las alertas son informativas. No descuentan existencias ni generan ajustes en los informes IMI. Los cuatro campos editables y los cálculos mensuales conservan su funcionamiento.

## Verificación

Probado con PHP 8.4 y MariaDB 11.8: actualización repetible, conservación de productos y fechas, altas M/I con o sin fecha, edición y borrado de la fecha, años bisiestos, rechazo de fechas imposibles, conflictos entre ediciones, estados y límite de 30 días, permisos y CSRF. Pendiente de prueba visual directamente en Windows/XAMPP.

No se incluyen datos de prueba ni contraseñas en los archivos entregados. Para una instalación nueva, la estructura actual del repositorio ya contempla el campo; no necesitas volver a ejecutar instalar.php si CATASSR ya está instalado.
