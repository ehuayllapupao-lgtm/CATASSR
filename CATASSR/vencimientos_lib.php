<?php
function vencimientos_ready(PDO $d): bool {
 try{q($d,'SELECT fecha_vencimiento FROM productos LIMIT 0');return true;}catch(PDOException $ex){return false;}
}
function vencimientos_fecha($value): ?string {
 if(!is_scalar($value))throw new RuntimeException('Fecha de vencimiento no válida.');
 $value=trim((string)$value);if($value==='')return null;
 if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/',$value,$m)||(int)$m[1]<1000||!checkdate((int)$m[2],(int)$m[3],(int)$m[1]))throw new RuntimeException('Ingrese una fecha de vencimiento válida en formato año-mes-día.');
 return $value;
}
function vencimientos_estado(?string $fecha,?DateTimeImmutable $hoy=null): array {
 if(!$fecha)return ['texto'=>'Sin registrar','fondo'=>'#edf1f5','color'=>'#53677b'];
 $hoy=($hoy??new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->setTime(0,0);
 if($fecha<$hoy->format('Y-m-d'))return ['texto'=>'Vencido','fondo'=>'#ffe5e9','color'=>'#a6203b'];
 if($fecha===$hoy->format('Y-m-d'))return ['texto'=>'Vence hoy','fondo'=>'#fff0d6','color'=>'#945600'];
 if($fecha<=$hoy->modify('+30 days')->format('Y-m-d'))return ['texto'=>'Próximo a vencer (30 días)','fondo'=>'#fff0d6','color'=>'#945600'];
 return ['texto'=>'Vigente','fondo'=>'#e1f4e9','color'=>'#216a46'];
}
