<?php
if(!isset($u,$panel,$showPriorities)||$u['rol']!=='admin'||!$showPriorities){http_response_code(403);exit('Acceso no permitido.');}
$piMonth=$panel['filtros']['mes'];
$piRows=[
 ['tipo'=>'sin_stock','cantidad'=>$panel['stock_disponible']?$panel['sin_stock_establecimientos']:null,'singular'=>'establecimiento con stock cero','plural'=>'establecimientos con stock cero','detalle'=>'Último IMI enviado hasta '.$piMonth,'icono'=>'caja','color'=>'pi-red'],
 ['tipo'=>'vencimientos','cantidad'=>$panel['vencimientos_disponibles']?count($panel['proximos']):null,'singular'=>'producto por vencer','plural'=>'productos por vencer','detalle'=>'Hoy y próximos 30 días · catálogo general','icono'=>'reloj','color'=>'pi-amber'],
 ['tipo'=>'vencidos','cantidad'=>$panel['vencimientos_disponibles']?count($panel['vencidos']):null,'singular'=>'producto vencido','plural'=>'productos vencidos','detalle'=>'Fechas del catálogo general','icono'=>'alerta','color'=>'pi-red'],
 ['tipo'=>'pendientes','cantidad'=>$panel['imi_disponible']?$panel['estados']['pendiente']:null,'singular'=>'IMI pendiente de envío','plural'=>'IMI pendientes de envío','detalle'=>'Sin informe registrado en '.$piMonth,'icono'=>'borrador','color'=>'pi-blue']
];
$piLink=static fn($tipo)=>'index.php?'.http_build_query(['page'=>'alertas','mes'=>$piMonth,'microred'=>0,'tipo'=>$tipo]);
?>
<dialog id="prioridades-inicio" class="pi-dialog" aria-labelledby="pi-title" aria-describedby="pi-intro">
 <div class="pi-header"><div><span class="pi-eyebrow">CATASSR · RESUMEN DE INGRESO</span><h2 id="pi-title">Prioridades de hoy</h2></div><form method="dialog"><button class="pi-close" autofocus aria-label="Cerrar prioridades de hoy">Cerrar</button></form></div>
 <div class="pi-content"><p id="pi-intro">Bienvenido, <?=e($u['nombre'])?>. Estas son las prioridades de toda la red.</p><p class="pi-date"><?=e($panel['ahora']->format('d/m/Y H:i'))?> · hora de Lima · IMI <?=e($piMonth)?></p>
 <?php if(in_array(null,array_column($piRows,'cantidad'),true)):?><p class="pi-unavailable">Algunos indicadores no están disponibles. Revisa el detalle en Alertas.</p><?php elseif(array_sum(array_column($piRows,'cantidad'))===0):?><p class="pi-clear">No se registran alertas en estas cuatro categorías.</p><?php endif;?>
 <div class="pi-rows"><?php foreach($piRows as $row):?><a class="pi-row" href="<?=e($piLink($row['tipo']))?>#reporte-alertas"><span class="pi-icon <?=$row['color']?>"><?=panel_admin_icono($row['icono'])?></span><div><span class="pi-row-label"><?=e($row['cantidad']===1?$row['singular']:$row['plural'])?></span><small><?=e($row['cantidad']===null?'Sin datos disponibles':$row['detalle'])?></small></div><strong class="pi-count"><?=panel_admin_num($row['cantidad'])?></strong><span class="pi-arrow"><?=panel_admin_icono('flecha')?></span></a><?php endforeach;?></div>
 </div>
 <div class="pi-footer"><button type="button" class="pi-secondary" data-pi-close>Ir al panel</button><a class="button" href="<?=e($piLink('sin_stock'))?>">Ver reportes de Alertas →</a></div>
</dialog>
<script src="prioridades_inicio.js?v=1" defer></script>
