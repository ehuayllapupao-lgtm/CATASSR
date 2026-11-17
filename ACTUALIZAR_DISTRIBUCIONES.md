# Distribuciones y almacén general

## Instalar sobre CATASSR existente

1. Descarga **descargas/CATASSR_DISTRIBUCIONES.zip** y descomprímelo.
2. Conserva una copia de CATASSR y de tu base de datos.
3. Copia la carpeta **CATASSR** del ZIP dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos.
4. Inicia Apache y MySQL e ingresa como administrador.
5. Abre `http://localhost/CATASSR/actualizar_distribuciones.php` y pulsa **Habilitar Distribuciones**. IMI debe estar habilitado previamente.
6. Recarga con Ctrl+F5. **Distribuciones** aparece después de **IMI**.

La actualización agrega siete tablas para programaciones, catálogos, saldos e historial. No modifica tus IMI ni borra cuentas, productos, establecimientos, contactos o asignaciones. Se puede repetir sin reiniciar saldos ni distribuciones. No ejecutes instalar.php ni importes la base inicial.

El archivo Excel se utilizó como referencia de estructura. No se importan sus cantidades de distribución, sus stocks ni su fecha como operaciones reales del almacén. El saldo inicial real se registra por el administrador.

## 1. Cargar el almacén general

En **Distribuciones → Almacén general**, selecciona el producto y registra un **Ingreso de stock** con la cantidad real y el documento o motivo. El primer ingreso puede representar el saldo inicial. Las cantidades están expresadas en la unidad del catálogo del producto.

Los ingresos son positivos. Los ajustes permiten cantidades positivas o negativas, siempre con un motivo y sin dejar un saldo negativo. Se conserva el historial: tipo de movimiento, cantidad, saldo anterior, saldo posterior, referencia, usuario y fecha del servidor. No se borran movimientos desde la interfaz.

El inventario es general por medicamento o insumo, compartido entre estrategias y áreas. Los borradores no descuentan ni reservan stock. El saldo disponible se vuelve a verificar al cerrar cada distribución.

## 2. Crear una programación

En **Programaciones**, puedes seleccionar una distribución existente por número/año o crear una nueva:

- Año y número de distribución (001 a 999, único dentro de ese año).
- Estrategia sanitaria.
- Área.
- Proyección de **3, 4, 5 o 6 meses**.

En **Estrategias y áreas**, el administrador puede agregar nombres. Se ofrecen como punto de partida Planificación Familiar, el programa materno neonatal del ejemplo y las áreas General, Hemocure y Hemocontrol. Son etiquetas configurables; no asignan automáticamente establecimientos a esos grupos ni separan inventarios.

Una programación puede incluir varios medicamentos. Selecciona el producto para analizar los establecimientos uno por uno.

## 3. Leer el análisis

La fuente es el último **IMI enviado** de cada establecimiento para ese producto. No se usan borradores como información consolidada.

- **Stock a la fecha:** saldo final informado en ese IMI. No significa un conteo físico en tiempo real; siempre se muestra el periodo y la fecha del registro que lo respalda.
- **CPMA:** promedio del consumo del mes fuente y de los meses registrados dentro de los dos meses calendario anteriores. Incluye ceros declarados y no inventa consumos de meses faltantes.
- **Disponibilidad:** stock informado / CPMA.
- **Pendiente de incorporar:** cantidades de distribuciones cerradas aún no declaradas como incorporadas al IMI. Se muestran separadas del stock informado.
- **Distribución proyectada:** máximo entre cero y el redondeo hacia arriba de (CPMA × meses elegidos − stock informado − entregas pendientes de incorporar).
- **Cantidad a distribuir:** la decisión del administrador. Puede ser distinta de la sugerencia.

La rúbrica aplica sobre el stock y consumo informados:

| Indicador | Regla |
| --- | --- |
| Desabastecido | Stock informado igual a cero. |
| Sub stock | Stock positivo y disponibilidad menor de 2 meses. |
| Stock normal | Disponibilidad entre 2 y 6 meses, inclusive. |
| Sobrestock | Disponibilidad mayor de 6 meses. |
| En blanco | Sin IMI o sin base suficiente para calcular la disponibilidad. |

Si hay stock pero el CPMA es cero, se indica sin consumo promedio y no se inventa una distribución proyectada. Si no hay información, no se interpreta como cero stock. El administrador puede asignar una cantidad manual, pero debe justificarla en Observación si no existe una sugerencia calculable.

La fecha de reporte corresponde al registro/actualización del IMI fuente. Un reporte antiguo sigue siendo antiguo: revisa su mes antes de distribuir.

## 4. Programar y guardar borradores

Usa la búsqueda por nombre, código o microred y el filtro de prioridad. **Usar sugerencias en filas visibles** completa las cantidades sugeridas solo en las filas que estás viendo. No reparte automáticamente un almacén insuficiente: revisa y ajusta las cantidades decididas.

Pulsa **Guardar borrador del producto** antes de cambiar de medicamento o cerrar. Cada producto se guarda con el análisis que respalda sus cantidades. Las filas ocultas por la búsqueda conservan sus valores; filtrarlas no las elimina de la programación.

Puedes cambiar la cobertura de 3 a 6 meses mediante **Actualizar análisis / proyección**. Se actualizan las sugerencias de los productos ya guardados y se conservan las cantidades que decidiste; revísalas nuevamente. Guarda primero los cambios en pantalla para no perderlos.

Los controles de versión rechazan sobrescribir cambios realizados por otra sesión.

## 5. Cerrar y registrar la salida

Despliega **Cerrar programación y descontar almacén**. Se muestra un resumen de todos los productos guardados, con stock disponible, total programado y saldo proyectado.

**Confirmar cierre de toda la programación** significa registrar las salidas reales del almacén. No es solo una vista previa. El sistema:

- Comprueba que existan cantidades positivas.
- Verifica que el IMI y las entregas pendientes no hayan cambiado desde el análisis guardado.
- Comprueba las observaciones requeridas y el stock de cada medicamento.
- Descuenta todos los productos en una transacción. Si cualquiera falla, no descuenta ninguno.
- Registra el saldo anterior, lo distribuido y el saldo posterior por producto.
- Fija la fecha de cierre con la fecha actual de Lima y deja las cantidades de la programación como consulta.

Un cierre repetido no vuelve a descontar. Las distribuciones cerradas no se editan ni se anulan desde esta versión. El historial de salida se conserva aunque posteriormente haya nuevos ingresos o ajustes.

Las salidas no modifican automáticamente el IMI del establecimiento. Se mantienen como entregas pendientes de incorporar para que la siguiente sugerencia no duplique lo ya programado y despachado.

## 6. Revisar entregas frente al nuevo IMI

Cuando un establecimiento envíe un IMI nuevo o actualizado y tenga entregas pendientes, aparece una alerta de conciliación. Hasta revisarlas no se genera una nueva sugerencia ni se permite cerrar cantidades adicionales para ese establecimiento/producto.

Abre la distribución cerrada y utiliza **Conciliar entregas**. Después de revisar el último IMI, el administrador declara una de estas dos opciones:

- **Incluida en IMI:** deja de contarse como pendiente porque el stock informado ya la refleja.
- **Aún no incluida · mantener pendiente:** conserva la cantidad pendiente, pero registra que se revisó el nuevo IMI y que no está incluida.

La confirmación es explícita; el sistema no adivina si el campo Recibido incluye una entrega particular. Se registra el IMI revisado, su versión, el usuario y la fecha. Si después vuelve a cambiar el IMI, se solicita una nueva revisión. Esta conciliación no cambia las cantidades históricas ni devuelve unidades al almacén.

Después de conciliar, actualiza el análisis de los borradores relacionados antes de cerrarlos.

## Exportación y permisos

Cada producto se puede exportar a **CSV para Excel** e imprimir. También puedes exportar **toda la programación guardada**, con una fila por establecimiento/producto y las instantáneas guardadas del análisis. La exportación utiliza únicamente el ámbito autorizado y las cantidades guardadas, no los cambios sin guardar. Al importar el CSV en Excel, trata los códigos como texto para conservar ceros iniciales.

- Administrador: estrategias, áreas, almacén, borradores, cierre y conciliación.
- Equipo técnico: consulta de distribuciones cerradas y almacén, sin modificaciones.
- Cabecera de microred: consulta de distribuciones cerradas de su microred.
- Encargado: consulta de distribuciones cerradas de su establecimiento.
- Doble función: establecimiento propio y microred asignada, siempre sin modificar distribuciones.

Los borradores son privados del administrador. El cierre de periodos IMI sigue controlando el ingreso de IMI; no abre ni cierra las programaciones de distribución.

## Verificación

Probado con PHP 8.4/MariaDB 11.8: habilitación repetible, catálogos, numeración, borradores, reglas de 2/6 meses, proyección, falta de información, cierre atómico, stock insuficiente, cierre/ingresos repetidos, saldos, historial, cambios de IMI, conciliación incluida y pendiente, actualización de snapshots, cierre de varios productos con rollback, exportación, permisos, ámbitos y CSRF. JavaScript verificado para búsqueda, sugerencias visibles, saldo proyectado y aviso de cambios sin guardar. Pendiente de prueba visual directamente en Windows/XAMPP.

Los archivos no incluyen datos, contraseñas ni cantidades de las pruebas. No se ha importado el archivo de ejemplo como datos operativos.
