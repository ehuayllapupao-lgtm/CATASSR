<?php
if(!isset($u,$panel)||$u['rol']!=='admin'){http_response_code(403);exit('Acceso no permitido.');}
$pf=$panel['filtros'];$imiOk=$panel['imi_disponible'];$stockOk=$panel['stock_disponible'];$expiryOk=$panel['vencimientos_disponibles'];
$count=$panel['estados'];$productMap=array_column($panel['productos'],null,'id');
$imiLink=static fn($state='',$micro=null)=>'imi.php?'.http_build_query(['mes'=>$pf['mes'],'microred'=>$micro??$pf['microred'],'estado'=>$state]);
$stockLabels=['desabastecido'=>'Desabastecido','sub_stock'=>'Sub stock','normal'=>'Stock normal','sobrestock'=>'Sobrestock','sin_informacion'=>'Sin información / sin base'];
?>
<div class="pa-dashboard">
 <div class="pa-toolbar"><div><p class="pa-intro">Lo que necesita tu atención, en un solo lugar.</p><small>Consulta actualizada a las <?=e($panel['ahora']->format('H:i'))?> · hora de Lima</small></div>
  <form method="get" class="pa-filters"><div><label for="pa-month">Mes del IMI</label><input id="pa-month" type="month" name="mes" min="2000-01" max="2099-12" value="<?=e($pf['mes'])?>" required></div><div><label for="pa-microred">Microred</label><select id="pa-microred" name="microred"><option value="0">Todas las microredes</option><?php foreach($micros as $m):?><option value="<?=$m['id']?>" <?=$pf['microred']==$m['id']?'selected':''?>><?=e($m['nombre'])?></option><?php endforeach;?></select></div><button>Aplicar filtros</button></form>
 </div>
 <?php if(!$imiOk):?><p class="alert">No se pudieron consultar los registros IMI. Los indicadores se mostrarán cuando el módulo tenga sus tablas disponibles.</p><?php endif;?>
 <div class="pa-kpis">
  <a class="pa-kpi pa-kpi-total" href="index.php?page=establecimientos&amp;microred=<?=$pf['microred']?>"><span class="pa-icon"><?=panel_admin_icono('red')?></span><div><span>Establecimientos</span><strong data-pa-total><?=$panel['total']?></strong><small><?=count($panel['microredes'])?> <?=count($panel['microredes'])===1?'microred':'microredes'?> en consulta</small></div></a>
  <a class="pa-kpi pa-kpi-sent" href="<?=e($imiLink('enviado'))?>"><span class="pa-icon"><?=panel_admin_icono('enviado')?></span><div><span>IMI enviados</span><strong data-pa-sent><?=panel_admin_num($imiOk?$count['enviado']:null)?></strong><small><?=$imiOk?panel_admin_porcentaje($count['enviado'],$panel['total']).' % de cumplimiento':'Sin datos disponibles'?></small></div></a>
  <a class="pa-kpi pa-kpi-draft" href="<?=e($imiLink('borrador'))?>"><span class="pa-icon"><?=panel_admin_icono('borrador')?></span><div><span>En borrador</span><strong data-pa-draft><?=panel_admin_num($imiOk?$count['borrador']:null)?></strong><small>Requieren completar</small></div></a>
  <a class="pa-kpi pa-kpi-pending" href="<?=e($imiLink('pendiente'))?>"><span class="pa-icon"><?=panel_admin_icono('reloj')?></span><div><span>Pendientes de envío</span><strong data-pa-pending><?=panel_admin_num($imiOk?$count['pendiente']:null)?></strong><small>Sin informe del mes elegido</small></div></a>
 </div>
 <div class="pa-main-grid">
  <section class="pa-card"><div class="pa-card-head"><div><h2>Avance del IMI por microred</h2><p>Enviados / establecimientos · <?=e($pf['mes'])?></p></div><span class="pa-label">SEGUIMIENTO</span></div>
   <div class="pa-legend"><span><i class="pa-dot pa-sent"></i>Enviados</span><span><i class="pa-dot pa-draft"></i>Borradores</span><span><i class="pa-dot pa-pending"></i>Pendientes</span></div>
   <div class="pa-networks">
   <?php foreach($panel['microredes'] as $m):$total=(int)$m['total'];?>
    <a class="pa-network" href="<?=e($imiLink('',(int)$m['id']))?>" aria-label="<?=e($m['nombre'].': '.($imiOk?$m['enviado'].' enviados de '.$total:'sin datos IMI'))?>"><div class="pa-network-name"><strong><?=e($m['nombre'])?></strong><span><?=$imiOk?$m['enviado'].' / '.$total:'Sin datos'?></span></div><div class="pa-track" aria-hidden="true"><?php if($imiOk&&$total):foreach(['enviado'=>'pa-sent','borrador'=>'pa-draft','pendiente'=>'pa-pending'] as $state=>$class):?><i class="<?=$class?>" style="width:<?=number_format(100*$m[$state]/$total,5,'.','')?>%"></i><?php endforeach;endif;?></div></a>
   <?php endforeach;?>
   <?php if(!$panel['microredes']):?><p class="pa-empty">No hay microredes registradas.</p><?php endif;?>
   </div><a class="pa-text-link" href="index.php?page=microredes&amp;mes=<?=e($pf['mes'])?>">Ver <?=count($micros)?> microredes →</a>
  </section>
  <section class="pa-card"><div class="pa-card-head"><h2>Prioridades de hoy</h2><span class="pa-label">ACCIONES</span></div>
   <a class="pa-priority" href="<?=e(panel_admin_url($pf,['detalle'=>'sin_stock']))?>#panel-detalle"><span class="pa-icon pa-red"><?=panel_admin_icono('caja')?></span><div><strong data-pa-zero><?=panel_admin_num($stockOk?$panel['sin_stock_establecimientos']:null)?> establecimientos con stock cero</strong><small>Revisar productos y preparar distribución</small></div><?=panel_admin_icono('flecha')?></a>
   <a class="pa-priority" href="<?=e(panel_admin_url($pf,['detalle'=>'vencimientos']))?>#panel-detalle"><span class="pa-icon pa-amber"><?=panel_admin_icono('alerta')?></span><div><strong data-pa-expiry><?=panel_admin_num($expiryOk?count($panel['proximos']):null)?> productos por vencer</strong><small>Catálogo general · hoy y próximos 30 días</small></div><?=panel_admin_icono('flecha')?></a>
   <?php if($expiryOk&&$panel['vencidos']):?><a class="pa-priority" href="<?=e(panel_admin_url($pf,['detalle'=>'vencidos']))?>#panel-detalle"><span class="pa-icon pa-red"><?=panel_admin_icono('alerta')?></span><div><strong><?=count($panel['vencidos'])?> productos vencidos</strong><small>Revisar fechas del catálogo general</small></div><?=panel_admin_icono('flecha')?></a><?php endif;?>
   <a class="pa-priority" href="<?=e($imiLink('pendiente'))?>"><span class="pa-icon pa-blue"><?=panel_admin_icono('borrador')?></span><div><strong><?=panel_admin_num($imiOk?$count['pendiente']:null)?> IMI pendientes</strong><small>Consultar establecimientos y responsables</small></div><?=panel_admin_icono('flecha')?></a>
   <p class="pa-footnote">Stock: último IMI enviado hasta el mes elegido. Vencimientos: fecha actual de Lima.</p>
  </section>
 </div>
 <div class="pa-bottom-grid">
  <section class="pa-card"><div class="pa-card-head"><div><h2>Vigilancia de abastecimiento</h2><p>Último IMI enviado hasta <?=e($pf['mes'])?></p></div><span class="pa-label">STOCK REPORTADO</span></div>
   <?php if($stockOk):?><div class="pa-stock-chart"><?php foreach($panel['stock'] as $state=>$value):?><div class="pa-stock-row" data-pa-state="<?=$state?>"><span><?=e($stockLabels[$state])?></span><div class="pa-stock-track"><i class="pa-stock-<?=$state?>" style="width:<?=number_format($panel['stock_total']?100*$value/$panel['stock_total']:0,5,'.','')?>%"></i></div><strong><?=panel_admin_num($value)?></strong></div><?php endforeach;?></div>
   <p class="pa-footnote">Se cuentan pares establecimiento / producto: <?=panel_admin_num($panel['stock_total'])?> en el ámbito elegido. Un establecimiento puede tener varios productos en alerta.</p>
   <?php if(!$panel['stock_total']):?><p class="pa-empty">Registra establecimientos y productos para visualizar la vigilancia.</p><?php endif;?>
   <?php else:?><p class="pa-empty">No hay datos de stock disponibles para la consulta.</p><?php endif;?>
   <details class="pa-method"><summary>Cómo se interpreta</summary><p>Desabastecido: stock cero. Sub stock: menos de 2 meses. Stock normal: de 2 a 6 meses. Sobrestock: más de 6 meses. Sin información / sin base: falta un dato de stock o un consumo promedio positivo.</p><p>Disponibilidad = stock final / CPMA. El CPMA promedia el consumo del último IMI enviado y de los informes enviados de los dos meses anteriores. No se usan borradores ni periodos posteriores al mes elegido. Un informe sin datos de un producto no equivale a stock cero.</p></details>
  </section>
  <section class="pa-card pa-distribution"><span class="pa-illustration"><?=panel_admin_icono('caja')?></span><span class="pa-label">SIGUIENTE PASO</span><h2>Programación de distribuciones</h2><p>Prepara las cantidades por establecimiento a partir de los últimos IMI enviados.</p><a class="button" href="distribuciones.php">Nueva distribución →</a><a class="pa-text-link" href="distribuciones.php?vista=almacen">Consultar almacén general →</a><small>Los indicadores de este panel muestran el stock reportado por los establecimientos.</small></section>
 </div>
 <?php if($pf['detalle']):?>
 <section class="pa-card" id="panel-detalle"><div class="pa-card-head"><div><h2><?=e(['sin_stock'=>'Productos reportados con stock cero','vencimientos'=>'Productos por vencer · próximos 30 días','vencidos'=>'Productos vencidos'][$pf['detalle']])?></h2><p><?=$pf['detalle']==='sin_stock'?'Último IMI enviado hasta '.e($pf['mes']):'Fechas del catálogo general · fecha de Lima '.e($panel['ahora']->format('d/m/Y'))?></p></div><a href="<?=e(panel_admin_url($pf))?>">Cerrar detalle</a></div>
 <?php if($pf['detalle']==='sin_stock'):?>
  <?php if($stockOk):?><div class="table"><table><thead><tr><th>Establecimiento</th><th>Microred</th><th>Producto</th><th>CPMA</th><th>Periodo / registro IMI</th><th>Acción</th></tr></thead><tbody><?php foreach($panel['sin_stock'] as $line):$e=$line['establecimiento'];$p=$productMap[$line['producto_id']];$r=$line['reporte'];?><tr><td><?=e($e['codigo'].' · '.$e['nombre'])?></td><td><?=e($e['microred'])?></td><td><?=e($p['codigo'].' · '.$p['nombre'])?></td><td><?=number_format($line['cpma'],2,',','.')?></td><td><?=e(substr($r['periodo'],0,7))?><br><small><?=e($r['actualizado_en'])?></small></td><td><a href="imi.php?<?=e(http_build_query(['establecimiento_id'=>$e['id'],'mes'=>substr($r['periodo'],0,7)]))?>">Consultar IMI</a></td></tr><?php endforeach;?><?php if(!$panel['sin_stock']):?><tr><td colspan="6">No hay productos reportados con stock cero en esta consulta.</td></tr><?php endif;?></tbody></table></div><a class="pa-text-link" href="distribuciones.php">Ir a distribuciones →</a><?php else:?><p>No se pudieron consultar los datos de stock.</p><?php endif;?>
 <?php else:$expiryRows=$pf['detalle']==='vencidos'?$panel['vencidos']:$panel['proximos'];?>
  <?php if($expiryOk):?><div class="table"><table><thead><tr><th>Código</th><th>Medicamento o insumo</th><th>Vencimiento</th></tr></thead><tbody><?php foreach($expiryRows as $p):?><tr><td><?=e($p['codigo'])?></td><td><?=e($p['nombre'])?></td><td><?=e((new DateTimeImmutable($p['fecha_vencimiento']))->format('d/m/Y'))?></td></tr><?php endforeach;?><?php if(!$expiryRows):?><tr><td colspan="3">No hay productos registrados en esta categoría.</td></tr><?php endif;?></tbody></table></div><a class="pa-text-link" href="index.php?page=productos">Revisar medicamentos e insumos →</a><?php else:?><p>Las fechas de vencimiento no están disponibles.</p><?php endif;?>
 <?php endif;?></section>
 <?php endif;?>
</div>
