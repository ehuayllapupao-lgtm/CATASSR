<?php
function alertas_tipo(array $input): string {
 $type=$input['tipo']??'sin_stock';
 if(!is_string($type)||!in_array($type,['sin_stock','vencimientos','vencidos','pendientes'],true))throw new InvalidArgumentException('Seleccione un reporte de alertas válido.');
 return $type;
}
function alertas_url(array $filters,string $type,array $extra=[]): string {
 return 'index.php?'.http_build_query(array_merge(['page'=>'alertas','mes'=>$filters['mes'],'microred'=>$filters['microred'],'tipo'=>$type],$extra));
}
function alertas_reporte(array $panel,string $type): array {
 $titles=['sin_stock'=>'Productos reportados con stock cero','vencimientos'=>'Productos por vencer','vencidos'=>'Productos vencidos','pendientes'=>'Establecimientos pendientes de enviar el IMI'];
 if(!isset($titles[$type]))throw new InvalidArgumentException('Reporte no válido.');
 $r=['titulo'=>$titles[$type],'columnas'=>[],'filas'=>[],'disponible'=>false,'resumen'=>'','ambito'=>''];$month=$panel['filtros']['mes'];
 $scope=$panel['filtros']['microred']?($panel['microredes'][$panel['filtros']['microred']]['nombre']??'Microred seleccionada'):'Todas las microredes';
 if($type==='sin_stock'){
  $r['disponible']=$panel['stock_disponible'];$r['ambito']=$scope.' · último IMI enviado hasta '.$month;
  $r['columnas']=['Código establecimiento','Establecimiento','Microred','Código producto','Medicamento o insumo','Stock reportado','CPMA','Periodo IMI','Registro IMI (servidor)'];$products=array_column($panel['productos'],null,'id');
  foreach($panel['sin_stock'] as $line){$e=$line['establecimiento'];$p=$products[$line['producto_id']];$source=$line['reporte'];
   $r['filas'][]=['celdas'=>[$e['codigo'],$e['nombre'],$e['microred'],$p['codigo'],$p['nombre'],'0',number_format($line['cpma'],2,',',''),substr($source['periodo'],0,7),$source['actualizado_en']],'enlace'=>'imi.php?'.http_build_query(['establecimiento_id'=>$e['id'],'mes'=>substr($source['periodo'],0,7)]),'accion'=>'Consultar IMI'];
  }
  $r['resumen']=count($r['filas']).' registros de establecimiento / producto · '.$panel['sin_stock_establecimientos'].' establecimientos con stock cero informado.';
 }elseif($type==='pendientes'){
  $r['disponible']=$panel['imi_disponible'];$r['ambito']=$scope.' · mes IMI '.$month;$r['columnas']=['Código establecimiento','Establecimiento','Microred','Responsable','Periodo IMI','Estado'];
  foreach($panel['pendientes'] as $e){$responsible=!empty($e['responsable'])?($e['responsable'].(!empty($e['responsable_activo'])?'':' (inactivo)')):'Pendiente de asignar';
   $row=['celdas'=>[$e['codigo'],$e['nombre'],$e['microred'],$responsible,$month,'Sin informe registrado'],'enlace'=>'imi.php?'.http_build_query(['establecimiento_id'=>$e['id'],'mes'=>$month]),'accion'=>'Consultar IMI'];
   if(!empty($e['responsable']))$row['ficha']='ficha_responsable.php?'.http_build_query(['establecimiento_id'=>$e['id']]);$r['filas'][]=$row;
  }
  $r['resumen']=count($r['filas']).' establecimientos sin informe registrado en el mes elegido. Los borradores se consultan por separado en IMI.';
 }else{
  $r['disponible']=$panel['vencimientos_disponibles'];$r['columnas']=['Código producto','Medicamento o insumo','Fecha de vencimiento','Estado'];
  $today=$panel['ahora']->format('Y-m-d');$r['ambito']='Catálogo general · fecha de Lima '.$panel['ahora']->format('d/m/Y');
  $rows=$type==='vencidos'?$panel['vencidos']:$panel['proximos'];
  foreach($rows as $p)$r['filas'][]=['celdas'=>[$p['codigo'],$p['nombre'],(new DateTimeImmutable($p['fecha_vencimiento']))->format('d/m/Y'),$type==='vencidos'?'Vencido':($p['fecha_vencimiento']===$today?'Vence hoy':'Próximo a vencer')],'enlace'=>'index.php?page=productos','accion'=>'Ver catálogo'];
  $r['resumen']=count($rows).' productos del catálogo general. '.($type==='vencidos'?'Vencidos antes de la fecha actual.':'Desde hoy hasta el '.$panel['ahora']->modify('+30 days')->format('d/m/Y').'.');
 }
 return $r;
}
function alertas_csv_valor($value): string {
 $s=(string)$value;
 // Evita fórmulas al abrir CSV en una hoja de cálculo y conserva los códigos con ceros.
 return preg_match('/^[\s]*[=+@-]|^[\t\r\n]|^0\d+$/u',$s)?"'".$s:$s;
}
function alertas_exportar(array $report,array $panel,string $type): void {
 $stamp=in_array($type,['vencimientos','vencidos'],true)?$panel['ahora']->format('Y-m-d'):$panel['filtros']['mes'];
 header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="CATASSR_Alertas_'.$type.'_'.$stamp.'.csv"');
 echo "\xEF\xBB\xBF";$f=fopen('php://output','w');
 $write=static function(array $row)use($f):void{fputcsv($f,array_map('alertas_csv_valor',$row),';','"','');};
 $write(['CATASSR · Alertas',$report['titulo']]);$write(['Ámbito',$report['ambito']]);$write(['Generado (hora de Lima)',$panel['ahora']->format('d/m/Y H:i')]);$write([$report['resumen']]);$write([]);$write($report['columnas']);
 foreach($report['filas'] as $row)$write($row['celdas']);fclose($f);exit;
}
