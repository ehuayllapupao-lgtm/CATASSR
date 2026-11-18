<?php
// Consultas de solo lectura del panel del administrador.
function panel_admin_filtros(array $input,array $micros,?DateTimeImmutable $now=null): array {
 $now=$now??new DateTimeImmutable('now',new DateTimeZone('America/Lima'));
 $month=$input['mes']??$now->format('Y-m');$raw=$input['microred']??'';
 if(!is_string($month)||!preg_match('/^20\d{2}-(0[1-9]|1[0-2])$/',$month)||!is_scalar($raw)||!preg_match('/^\d{0,9}$/',(string)$raw))throw new InvalidArgumentException('Seleccione un mes y una microred válidos.');
 $micro=(int)$raw;if($micro&&!in_array($micro,array_map('intval',array_column($micros,'id')),true))throw new InvalidArgumentException('La microred seleccionada no existe.');
 $detail=$input['detalle']??'';if(!is_string($detail)||!in_array($detail,['','sin_stock','vencimientos','vencidos'],true))throw new InvalidArgumentException('Detalle del panel no válido.');
 return ['mes'=>$month,'microred'=>$micro,'detalle'=>$detail];
}
function panel_admin_indicador(?int $stock,?float $cpma): string {
 if($stock===null)return 'sin_informacion';if($stock===0)return 'desabastecido';
 if($cpma===null||$cpma<=0)return 'sin_informacion';$months=$stock/$cpma;
 return $months<2?'sub_stock':($months<=6?'normal':'sobrestock');
}
function panel_admin_datos(PDO $d,array $filters,array $est,array $micros,?DateTimeImmutable $now=null): array {
 $now=($now??new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->setTimezone(new DateTimeZone('America/Lima'));
 $selected=array_values(array_filter($est,fn($e)=>!$filters['microred']||(int)$e['microred_id']===$filters['microred']));
 $estById=array_column($selected,null,'id');$networks=[];
 foreach($micros as $m)if(!$filters['microred']||(int)$m['id']===$filters['microred'])$networks[$m['id']]=['id'=>(int)$m['id'],'nombre'=>$m['nombre'],'total'=>0,'enviado'=>0,'borrador'=>0,'pendiente'=>0];
 foreach($selected as $e)$networks[$e['microred_id']]['total']++;
 $data=['filtros'=>$filters,'ahora'=>$now,'total'=>count($selected),'microredes'=>$networks,'estados'=>['enviado'=>0,'borrador'=>0,'pendiente'=>0],'imi_disponible'=>false,'stock_disponible'=>false,'vencimientos_disponibles'=>false,'productos'=>[],'stock'=>['desabastecido'=>0,'sub_stock'=>0,'normal'=>0,'sobrestock'=>0,'sin_informacion'=>0],'stock_total'=>0,'sin_stock'=>[],'sin_stock_establecimientos'=>0,'proximos'=>[],'vencidos'=>[]];
 $data['productos']=q($d,'SELECT id,codigo,nombre FROM productos ORDER BY nombre')->fetchAll();$productIds=array_fill_keys(array_column($data['productos'],'id'),true);
 try{
  $states=q($d,'SELECT establecimiento_id,estado FROM imi_reportes WHERE periodo=?',[$filters['mes'].'-01'])->fetchAll();$byState=array_column($states,'estado','establecimiento_id');
  foreach($selected as $e){$state=$byState[$e['id']]??'pendiente';$data['estados'][$state]++;$data['microredes'][$e['microred_id']][$state]++;}
  $data['imi_disponible']=true;
 }catch(PDOException $ex){error_log('Panel IMI: '.$ex->getMessage());}
 try{
  $scope=$filters['microred']?' AND e.microred_id=?':'';$args=[$filters['mes'].'-01',$filters['mes'].'-01'];if($filters['microred'])$args[]=$filters['microred'];
  $latest=q($d,"SELECT r.id,r.establecimiento_id,r.periodo,r.actualizado_en FROM imi_reportes r JOIN establecimientos e ON e.id=r.establecimiento_id WHERE r.estado='enviado' AND r.periodo<=? AND NOT EXISTS(SELECT 1 FROM imi_reportes newer WHERE newer.establecimiento_id=r.establecimiento_id AND newer.estado='enviado' AND newer.periodo<=? AND newer.periodo>r.periodo)".$scope,$args)->fetchAll();
  $latestByEst=array_column($latest,null,'establecimiento_id');$rows=[];
  if($latest){$ids=array_column($latest,'id');$placeholders=implode(',',array_fill(0,count($ids),'?'));
   $rows=q($d,"SELECT latest.establecimiento_id,x.producto_id,AVG(x.consumo) cpma,MAX(CASE WHEN r.id=latest.id THEN x.stock_final ELSE NULL END) stock FROM imi_reportes latest JOIN imi_reportes r ON r.establecimiento_id=latest.establecimiento_id AND r.estado='enviado' AND r.periodo BETWEEN DATE_SUB(latest.periodo,INTERVAL 2 MONTH) AND latest.periodo JOIN imi_detalles x ON x.reporte_id=r.id WHERE latest.id IN ($placeholders) GROUP BY latest.establecimiento_id,x.producto_id HAVING stock IS NOT NULL",$ids)->fetchAll();
  }
  $known=0;$zeroEst=[];
  foreach($rows as $r){$eid=(int)$r['establecimiento_id'];$pid=(int)$r['producto_id'];if(!isset($estById[$eid],$productIds[$pid]))continue;
   $stock=(int)$r['stock'];$cpma=(float)$r['cpma'];$state=panel_admin_indicador($stock,$cpma);$data['stock'][$state]++;$known++;
   if($stock===0){$zeroEst[$eid]=true;$data['sin_stock'][]=['establecimiento'=>$estById[$eid],'producto_id'=>$pid,'cpma'=>$cpma,'reporte'=>$latestByEst[$eid]];}
  }
  $data['stock_total']=count($selected)*count($productIds);$data['stock']['sin_informacion']+=$data['stock_total']-$known;
  $data['sin_stock_establecimientos']=count($zeroEst);$data['stock_disponible']=true;
 }catch(PDOException $ex){error_log('Panel stock: '.$ex->getMessage());}
 try{
  $today=$now->format('Y-m-d');$end=$now->modify('+30 days')->format('Y-m-d');
  $data['proximos']=q($d,'SELECT id,codigo,nombre,fecha_vencimiento FROM productos WHERE fecha_vencimiento BETWEEN ? AND ? ORDER BY fecha_vencimiento,nombre',[$today,$end])->fetchAll();
  $data['vencidos']=q($d,'SELECT id,codigo,nombre,fecha_vencimiento FROM productos WHERE fecha_vencimiento<? ORDER BY fecha_vencimiento,nombre',[$today])->fetchAll();$data['vencimientos_disponibles']=true;
 }catch(PDOException $ex){error_log('Panel vencimientos: '.$ex->getMessage());}
 return $data;
}
function panel_admin_url(array $filters,array $extra=[]): string {
 return 'index.php?'.http_build_query(array_merge(['mes'=>$filters['mes'],'microred'=>$filters['microred']],$extra));
}
function panel_admin_num($value): string {return $value===null?'—':number_format((float)$value,0,',','.');}
function panel_admin_porcentaje(int $n,int $total): string {return number_format($total?100*$n/$total:0,1,',','.');}
function panel_admin_icono(string $name): string {
 $paths=['red'=>'<path d="M12 3v5M5 14V9h14v5M12 9v5"/><rect x="2" y="15" width="6" height="6" rx="1"/><rect x="9" y="15" width="6" height="6" rx="1"/><rect x="16" y="15" width="6" height="6" rx="1"/><rect x="9" y="2" width="6" height="5" rx="1"/>','enviado'=>'<path d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4M8 11l4 4L22 5"/>','borrador'=>'<path d="M13 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-8M14 7l3 3M10 17l4-1L22 8l-3-3-8 8z"/>','reloj'=>'<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>','alerta'=>'<path d="m12 3 10 18H2zM12 9v5m0 3v1"/>','caja'=>'<path d="m12 2 10 5v10l-10 5L2 17V7zM2 7l10 5 10-5M12 12v10M7 4.5l10 5"/>','flecha'=>'<path d="m9 5 7 7-7 7"/>'];
 return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name]??$paths['red']).'</svg>';
}
