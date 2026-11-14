# IMI de prueba — Actualización para XAMPP

## Si CATASSR ya está instalado

1. Descarga **descargas/CATASSR_IMI_PRUEBA.zip** desde este repositorio y descomprímelo.
2. Guarda una copia de tu carpeta CATASSR y de la base desde phpMyAdmin.
3. Copia la carpeta **CATASSR** que viene en el ZIP dentro de `C:\xampp\htdocs\` y acepta reemplazar los archivos existentes.
4. Inicia Apache y MySQL. Ingresa como administrador en `http://localhost/CATASSR/`.
5. Abre `http://localhost/CATASSR/actualizar_imi.php` y pulsa **Habilitar módulo IMI**.
6. Abre **Periodos IMI**, selecciona el mes y pulsa **Abrir / actualizar plazo**. Los meses no habilitados quedan de consulta.
7. Encontrarás **IMI** justo debajo de **Panel principal**. Consulta ACTUALIZAR_PERIODOS_IMI.md para el control de ingreso.

El ZIP de actualización contiene solo los archivos nuevos o modificados. No incluye config.php, config.local.php, el instalador ni datos de ejemplo. Conserva tu conexión, tus usuarios, asignaciones, establecimientos y medicamentos. La actualización añade tres tablas con CREATE TABLE IF NOT EXISTS. No vuelvas a ejecutar instalar.php ni importes la base inicial.

## Probar el primer informe

1. Como administrador, abre **IMI**, elige un establecimiento y un mes.
2. Antes de guardar el primer informe de ese establecimiento, despliega **Administrador: registrar saldo de apertura** y guarda las existencias iniciales reales. Si no lo haces, el saldo inicial será cero.
3. Completa exclusivamente las cuatro columnas verdes: stock según medicamentos, recibido, entregado a usuarias (consumo), y ajustes por vencidos/pérdidas/deterioro. Usa cantidades enteras no negativas; escribe 0 si no hubo movimiento.
4. Verifica los resultados automáticos y pulsa **Guardar borrador** o **Enviar IMI**.
5. Tras enviar el informe, abre el mes siguiente: el saldo inicial viene del cierre anterior. Los meses se registran consecutivamente.
6. El informe enviado queda de consulta. El administrador puede reabrir únicamente el más reciente. Una vez creado el periodo siguiente, el anterior no se modifica para evitar alterar saldos posteriores.

Ejemplo para probar con un producto: apertura 100, stock según 50, recibido 20, consumo 30 y ajustes 5. Resultado: total 120, stock final 85, CPM 30 (primer mes), cobertura 2.83 meses, pedido sugerido por saldo final 5 y sugerido por stock según 40. Es un ejemplo; el archivo no carga estas cantidades en tu base.

## Reglas propuestas para esta prueba

- Inicial: cierre anterior o apertura registrada antes del primer informe.
- Total: inicial + recibido.
- Final: total − consumo − ajustes. Un saldo negativo impide guardar el informe completo.
- CPM: consumo promedio del mes actual y los meses registrados dentro de los dos meses calendario anteriores; el primer mes usa solo su consumo. Los meses con cero consumo registrado sí cuentan.
- Cobertura: final / CPM.
- Pedido por saldo final: máximo(0, redondeo hacia arriba(CPM × 3 − saldo final)).
- Segunda sugerencia: la misma fórmula, usando stock según medicamentos como saldo declarado. Este dato es comparativo y no sustituye el saldo calculado.
- Si CPM es cero, cobertura y pedidos muestran Sin base de consumo.
- Alertas de prueba: cobertura baja si es menor de 1 mes, sobrestock si supera 6 meses, sin existencias si el saldo es cero y existe consumo promedio.

Estas reglas, la cobertura objetivo de 3 meses y la interpretación de stock según medicamentos deben validarse antes del uso operativo. No son una norma institucional aprobada.

## Permisos

Administrador: apertura, registro de cualquier establecimiento, consulta y reapertura del último enviado.
Encargado: registro del establecimiento asignado en periodos abiertos.
Cabecera: consulta y exportación exclusivamente de su microred.
Equipo técnico: consulta y exportación general.

Los permisos se verifican en el servidor, también al exportar. Las fórmulas se recalculan en PHP al guardar y se ignoran los resultados automáticos que envíe el navegador. Hay protección CSRF y control de versión para impedir sobrescribir cambios de otra sesión.

## Descargar y revisar

Desde IMI puedes exportar **CSV para Excel** e imprimir. El CSV contiene los valores guardados; guarda primero para incluir tus cambios.

También se incluye **plantillas/IMI_CATASSR_PRUEBA.xlsx** con hojas mensuales, cuatro campos editables y fórmulas. No está vinculado automáticamente a la base web.

## Verificación

Probado mediante PHP 8.4 y MariaDB 11.8: actualización sin reinstalar, apertura, cálculos, arrastre, CPM, solicitudes, ceros, errores, borradores/envíos, reapertura, versiones, permisos, aislamiento y exportación. JavaScript verificado sintácticamente. Excel recalculado para validar fórmulas. Pendiente de prueba visual y verificación directamente en Windows/XAMPP.

La fecha de modificación mostrada utiliza la hora del servidor MySQL. Los datos usados en las pruebas se guardaron solo en una base de prueba local, no en los archivos entregados.
