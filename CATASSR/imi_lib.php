<?php
// Reglas de prueba: CPM de hasta tres meses registrados; cobertura objetivo 3 meses.
const IMI_COBERTURA=3;
function imi_ready(PDO $d): bool {try{q($d,'SELECT 1 FROM imi_detalles LIMIT 1');q($d,'SELECT 1 FROM imi_reportes LIMIT 1');q($d,'SELECT 1 FROM imi_apertura LIMIT 1');q($d,'SELECT 1 FROM imi_periodos LIMIT 1');return true;}catch(PDOException $ex){return false;}}
function imi_establecimientos(PDO $d,array $u): array {
 [$where,$args]=usuario_ambito($u);
 return q($d,'SELECT e.*,m.nombre microred FROM establecimientos e JOIN microredes m ON m.id=e.microred_id WHERE '.$where.' ORDER BY e.nombre',$args)->fetchAll();
}
function imi_num($value): int {if(!is_scalar($value)||!preg_match('/^\d{1,9}$/',(string)$value))throw new RuntimeException('Ingrese cantidades enteras entre 0 y 999999999 en los cuatro campos de cada producto.');return (int)$value;}
function imi_calculo(int $inicial,array $raw,array $history): array {
 $total=$inicial+$raw['recibido'];$final=$total-$raw['consumo']-$raw['ajuste'];
 $cpm=($raw['consumo']+array_sum($history))/(1+count($history));
 return ['inicial'=>$inicial,'total'=>$total,'final'=>$final,'cpm'=>$cpm,'meses'=>$cpm>0?$final/$cpm:null,'solicitar'=>$cpm>0?max(0,(int)ceil(round($cpm*IMI_COBERTURA-$final,8))):null,'segun'=>$cpm>0?max(0,(int)ceil(round($cpm*IMI_COBERTURA-$raw['stock_segun'],8))):null,'estado'=>$final<0?'REVISAR SALDO':($cpm<=0?'SIN CONSUMO PROMEDIO':($final===0?'SIN EXISTENCIAS':($final/$cpm<1?'COBERTURA BAJA':($final/$cpm>6?'SOBRESTOCK':'COBERTURA ADECUADA'))))];
}
function imi_context(PDO $d,int $eid,string $period): array {
 $products=q($d,'SELECT * FROM productos ORDER BY id')->fetchAll();
 $header=q($d,'SELECT * FROM imi_reportes WHERE establecimiento_id=? AND periodo=?',[$eid,$period])->fetch()?:null;
 $latest=q($d,'SELECT * FROM imi_reportes WHERE establecimiento_id=? ORDER BY periodo DESC LIMIT 1',[$eid])->fetch()?:null;
 $previous=q($d,'SELECT * FROM imi_reportes WHERE establecimiento_id=? AND periodo<? ORDER BY periodo DESC LIMIT 1',[$eid,$period])->fetch()?:null;
 $opening=q($d,'SELECT producto_id,saldo FROM imi_apertura WHERE establecimiento_id=?',[$eid])->fetchAll(PDO::FETCH_KEY_PAIR);
 $prev=$previous?q($d,'SELECT producto_id,stock_final FROM imi_detalles WHERE reporte_id=?',[$previous['id']])->fetchAll(PDO::FETCH_KEY_PAIR):[];
 $saved=$header?q($d,'SELECT * FROM imi_detalles WHERE reporte_id=?',[$header['id']])->fetchAll():[];$saved=array_column($saved,null,'producto_id');
 $hist=[];$start=(new DateTimeImmutable($period))->modify('-2 months')->format('Y-m-d');
 foreach(q($d,'SELECT x.producto_id,x.consumo FROM imi_detalles x JOIN imi_reportes r ON r.id=x.reporte_id WHERE r.establecimiento_id=? AND r.periodo>=? AND r.periodo<?',[$eid,$start,$period])->fetchAll() as $h)$hist[$h['producto_id']][]=(int)$h['consumo'];
 return compact('products','header','latest','previous','opening','prev','saved','hist');
}
function imi_rows(array $ctx,array $submitted=[]): array {
 $rows=[];foreach($ctx['products'] as $p){$pid=$p['id'];$s=$ctx['saved'][$pid]??[];$raw=[];
 foreach(['stock_segun','recibido','consumo','ajuste'] as $key)$raw[$key]=isset($submitted[$pid][$key])?imi_num($submitted[$pid][$key]):(int)($s[$key]??0);
 $initial=(int)($ctx['header']&&isset($s['stock_inicial'])?$s['stock_inicial']:($ctx['prev'][$pid]??$ctx['opening'][$pid]??0));
 $rows[]=['producto'=>$p,'raw'=>$raw,'history'=>$ctx['hist'][$pid]??[],'calc'=>imi_calculo($initial,$raw,$ctx['hist'][$pid]??[])];
 }return $rows;
}
function imi_format($n): string {return $n===null?'Sin base de consumo':number_format((float)$n,2,'.',',');}

function imi_periodo_abierto(?array $p): bool {
 if(!$p || $p['estado']!=='abierto')return false;
 $now=(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('Y-m-d H:i:s');
 return (!$p['inicio'] || $now>=$p['inicio']) && (!$p['fin'] || $now<=$p['fin']);
}
function imi_periodo_texto(?array $p): string {
 if(!$p)return 'Sin habilitar · solo consulta';
 if($p['estado']==='cerrado')return 'Cerrado · solo consulta';
 $now=(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('Y-m-d H:i:s');
 if($p['inicio'] && $now<$p['inicio'])return 'Programado · aún no admite ingreso';
 if($p['fin'] && $now>$p['fin'])return 'Plazo vencido · solo consulta';
 return 'Abierto para ingreso';
}
function imi_fecha_ventana($value): ?string {
 if(!is_scalar($value))throw new RuntimeException('Fecha de ingreso no válida.');
 $value=trim((string)$value);if($value==='')return null;
 $date=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$value,new DateTimeZone('America/Lima'));
 if(!$date || $date->format('Y-m-d\TH:i')!==$value)throw new RuntimeException('Fecha de ingreso no válida.');
 return $date->format('Y-m-d H:i:00');
}
