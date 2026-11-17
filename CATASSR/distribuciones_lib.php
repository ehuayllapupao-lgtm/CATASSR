<?php
const DIST_ESTRATEGIA_FIJA='PROGRAMA 002 SALUD MATERNO NEONATAL';
const DIST_AREAS_FIJAS=['PLANIFICACION FAMILIAR','INSUMOS','INSTRUMENTOS MEDICOS','MEDICAMENTOS'];
function dist_areas_opciones(PDO $d): array {
 $out=[];foreach(DIST_AREAS_FIJAS as $name){$id=q($d,'SELECT id FROM dist_areas WHERE nombre=?',[$name])->fetchColumn();if($id)$out[]=['id'=>(int)$id,'nombre'=>$name];}return $out;
}
function dist_valid_areas($input,array $options): array {
 if(!is_array($input)||!$input||count($input)>4)throw new RuntimeException('Seleccione al menos una de las cuatro áreas.');
 $allowed=array_column($options,'id');$ids=[];foreach($input as $raw){$id=dist_int($raw,1);if(!in_array($id,$allowed,true))throw new RuntimeException('Área no válida. Seleccione únicamente las áreas disponibles.');$ids[$id]=$id;}return array_values($ids);
}
function dist_ready(PDO $d): bool {try{foreach(['dist_control','dist_programacion_areas','dist_estrategias','dist_areas','dist_almacen','dist_programaciones','dist_detalles','dist_movimientos','imi_reportes','imi_detalles'] as $t)q($d,'SELECT 1 FROM '.$t.' LIMIT 1');return (bool)q($d,'SELECT id FROM dist_control WHERE id=1')->fetch()&&count(dist_areas_opciones($d))===4&&(bool)q($d,'SELECT id FROM dist_estrategias WHERE nombre=?',[DIST_ESTRATEGIA_FIJA])->fetch();}catch(PDOException $ex){return false;}}
function dist_int($v,int $min=0,int $max=999999999): int {if(!is_scalar($v)||!preg_match('/^\d{1,12}$/',(string)$v)||(int)$v<$min||(int)$v>$max)throw new RuntimeException('Cantidad o selección no válida.');return (int)$v;}
function dist_text($v,int $max=250,bool $required=false): string {if(!is_string($v))throw new RuntimeException('Texto no válido.');$v=trim($v);if(($required&&$v==='')||preg_match_all('/./us',$v)>$max)throw new RuntimeException('Complete el texto requerido y respete el tamaño máximo.');return $v;}
function dist_num($v): string {return $v===null?'Sin información':number_format((float)$v,is_float($v)||strpos((string)$v,'.')!==false?2:0,'.',',');}
function dist_badge(string $state): string {return ['desabastecido'=>'Desabastecido','sub_stock'=>'Sub stock','normal'=>'Stock normal','sobrestock'=>'Sobrestock','en_blanco'=>'En blanco'][$state]??$state;}
function dist_calculate(?int $stock,?float $cpma,int $months,int $pending=0,bool $reconcile=false): array {
 $coverage=$stock!==null&&$cpma!==null&&$cpma>0?$stock/$cpma:null;
 $state=$stock===null?'en_blanco':($stock===0?'desabastecido':($coverage===null?'en_blanco':($coverage<2?'sub_stock':($coverage<=6?'normal':'sobrestock'))));
 $suggested=$cpma!==null&&$cpma>0&&$stock!==null&&!$reconcile?max(0,(int)ceil(round($cpma*$months-$stock-$pending,8))):null;
 return ['stock'=>$stock,'cpma'=>$cpma,'cobertura'=>$coverage,'indicador'=>$state,'sugerida'=>$suggested,'pendiente_antes'=>$pending,'conciliar'=>$reconcile];
}
function dist_est(PDO $d,array $u): array {[$scope,$args]=usuario_ambito($u);return q($d,'SELECT e.*,m.nombre microred FROM establecimientos e JOIN microredes m ON m.id=e.microred_id WHERE '.$scope.' ORDER BY m.nombre,e.nombre',$args)->fetchAll();}
function dist_analysis(PDO $d,int $pid,int $months): array {
 $reports=q($d,'SELECT x.stock_final,x.consumo,r.id fuente_id,r.version fuente_version,r.periodo fuente_periodo,r.actualizado_en fuente_fecha,r.establecimiento_id FROM imi_detalles x JOIN imi_reportes r ON r.id=x.reporte_id WHERE x.producto_id=? AND r.estado=\'enviado\' ORDER BY r.periodo DESC,r.id DESC',[$pid])->fetchAll();
 $group=[];foreach($reports as $r)$group[$r['establecimiento_id']][]=$r;
 $pending=[];foreach(q($d,'SELECT x.establecimiento_id,x.cantidad,COALESCE(x.pendiente_verificado_id,x.fuente_id) fuente_id,COALESCE(x.pendiente_verificado_version,x.fuente_version) fuente_version FROM dist_detalles x JOIN dist_programaciones p ON p.id=x.programacion_id WHERE x.producto_id=? AND p.estado=\'cerrado\' AND x.cantidad>0 AND x.incorporado_en IS NULL',[$pid])->fetchAll() as $x)$pending[$x['establecimiento_id']][]=$x;
 $out=[];foreach(q($d,'SELECT id FROM establecimientos')->fetchAll() as $est){$eid=$est['id'];$latest=$group[$eid][0]??null;$cpma=null;$stock=null;
  if($latest){$start=(new DateTimeImmutable($latest['fuente_periodo']))->modify('-2 months')->format('Y-m-d');$cons=[];foreach($group[$eid] as $r)if($r['fuente_periodo']>=$start)$cons[]=(int)$r['consumo'];$cpma=array_sum($cons)/count($cons);$stock=(int)$latest['stock_final'];}
  $sum=0;$reconcile=false;foreach($pending[$eid]??[] as $x){$sum+=(int)$x['cantidad'];if($latest&&((int)$latest['fuente_id']!==(int)$x['fuente_id']||(int)$latest['fuente_version']!==(int)$x['fuente_version']))$reconcile=true;}
  $out[$eid]=array_merge(['fuente_id'=>$latest['fuente_id']??null,'fuente_version'=>$latest['fuente_version']??null,'fuente_periodo'=>$latest['fuente_periodo']??null,'fuente_fecha'=>$latest['fuente_fecha']??null],dist_calculate($stock,$cpma,$months,$sum,$reconcile));
 }return $out;
}
function dist_store(PDO $d,int $id,int $pid,int $eid,int $quantity,array $a,string $note): void {
 q($d,'INSERT INTO dist_detalles(programacion_id,producto_id,establecimiento_id,cantidad,fuente_id,fuente_version,fuente_periodo,fuente_fecha,stock,cpma,cobertura,indicador,pendiente_antes,sugerida,observacion) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE cantidad=VALUES(cantidad),fuente_id=VALUES(fuente_id),fuente_version=VALUES(fuente_version),fuente_periodo=VALUES(fuente_periodo),fuente_fecha=VALUES(fuente_fecha),stock=VALUES(stock),cpma=VALUES(cpma),cobertura=VALUES(cobertura),indicador=VALUES(indicador),pendiente_antes=VALUES(pendiente_antes),sugerida=VALUES(sugerida),observacion=VALUES(observacion)',[$id,$pid,$eid,$quantity,$a['fuente_id'],$a['fuente_version'],$a['fuente_periodo'],$a['fuente_fecha'],$a['stock'],$a['cpma'],$a['cobertura'],$a['indicador'],$a['pendiente_antes'],$a['sugerida'],$note]);
}
function dist_lock(PDO $d): void {$d->beginTransaction();if(!q($d,'SELECT id FROM dist_control WHERE id=1 FOR UPDATE')->fetch())throw new RuntimeException('Habilite primero el módulo.');}
function dist_lock_est(PDO $d,array $ids): void {$ids=array_unique(array_map('intval',$ids));sort($ids);foreach($ids as $eid)q($d,'SELECT id FROM establecimientos WHERE id=? FOR UPDATE',[$eid]);}
function dist_close(PDO $d,array $p,array $u): void {
 $cells=q($d,'SELECT * FROM dist_detalles WHERE programacion_id=? AND cantidad>0 ORDER BY producto_id,establecimiento_id',[$p['id']])->fetchAll();if(!$cells)throw new RuntimeException('Guarde al menos una cantidad a distribuir antes de cerrar.');
 dist_lock_est($d,array_column($cells,'establecimiento_id'));$analyses=[];$totals=[];
 foreach($cells as $x){$pid=$x['producto_id'];if(!isset($analyses[$pid]))$analyses[$pid]=dist_analysis($d,(int)$pid,(int)$p['meses']);$a=$analyses[$pid][$x['establecimiento_id']];
  if($a['conciliar'])throw new RuntimeException('Hay entregas pendientes de conciliar con un IMI nuevo. Confirme su incorporación antes de cerrar.');
  if((int)$a['fuente_id']!==(int)$x['fuente_id']||(int)$a['fuente_version']!==(int)$x['fuente_version']||(int)$a['pendiente_antes']!==(int)$x['pendiente_antes']||($a['sugerida']===null)!==($x['sugerida']===null)||($a['sugerida']!==null&&(int)$a['sugerida']!==(int)$x['sugerida']))throw new RuntimeException('Cambió el IMI o las entregas pendientes. Actualice el análisis y revise las cantidades antes de cerrar.');
  if($a['sugerida']===null&&trim($x['observacion'])==='')throw new RuntimeException('Las cantidades sin base de consumo requieren una observación.');
  $totals[$pid]=($totals[$pid]??0)+(int)$x['cantidad'];
 }
 foreach($totals as $pid=>$qty){q($d,'INSERT IGNORE INTO dist_almacen(producto_id,saldo) VALUES(?,0)',[$pid]);$before=(int)q($d,'SELECT saldo FROM dist_almacen WHERE producto_id=?',[$pid])->fetchColumn();if($before<$qty)throw new RuntimeException('Stock insuficiente en almacén para el producto '.$pid.'. Disponible: '.$before.'; programado: '.$qty.'.');$after=$before-$qty;
  q($d,'UPDATE dist_almacen SET saldo=? WHERE producto_id=?',[$after,$pid]);
  q($d,'INSERT INTO dist_movimientos(producto_id,programacion_id,tipo,cantidad,saldo_antes,saldo_despues,referencia,solicitud,creado_por) VALUES(?,?,\'salida\',?,?,?,?,?,?)',[$pid,$p['id'],-$qty,$before,$after,'Distribución '.$p['numero'].'/'.$p['anio'],'cierre:'.$p['id'].':'.$pid,$u['id']]);
 }
 $date=(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('Y-m-d');
 q($d,'UPDATE dist_programaciones SET estado=\'cerrado\',version=version+1,cerrado_por=?,cerrado_en=NOW(),fecha_cierre=? WHERE id=?',[$u['id'],$date,$p['id']]);
}
