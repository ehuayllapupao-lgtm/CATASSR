<?php
require __DIR__.'/app.php';require __DIR__.'/imi_lib.php';
try{$d=db();$u=isset($_SESSION['uid'])?q($d,'SELECT * FROM usuarios WHERE id=? AND activo=1',[$_SESSION['uid']])->fetch():null;}catch(PDOException $ex){go('index.php');}
if(!$u)go('index.php');if($u['rol']!=='admin'){http_response_code(403);exit('Solo el administrador puede gestionar los periodos de ingreso.');}
if(!imi_ready($d))go('actualizar_imi.php');
$month=(string)($_POST['mes']??$_GET['mes']??(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('Y-m'));
if(!preg_match('/^20\d{2}-(0[1-9]|1[0-2])$/',$month)){http_response_code(422);exit('Mes no válido.');}
$period=$month.'-01';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf();try{
 $action=$_POST['action']??'';if(!in_array($action,['abrir','cerrar'],true))throw new RuntimeException('Acción no válida.');
 // Closing never depends on validity of the old schedule: it must always be possible.
 $start=$action==='abrir'?imi_fecha_ventana($_POST['inicio']??''):null;
 $end=$action==='abrir'?imi_fecha_ventana($_POST['fin']??''):null;
 if($start&&$end&&$start>$end)throw new RuntimeException('El fin debe ser igual o posterior al inicio.');
 $d->beginTransaction();$old=q($d,'SELECT * FROM imi_periodos WHERE periodo=? FOR UPDATE',[$period])->fetch()?:null;
 if((int)($_POST['version']??0)!==(int)($old['version']??0))throw new RuntimeException('Otro administrador cambió este periodo. Recargue antes de guardar.');
 if($action==='cerrar'){$start=$old['inicio']??null;$end=$old['fin']??null;}
 $state=$action==='abrir'?'abierto':'cerrado';
 if($old)q($d,'UPDATE imi_periodos SET estado=?,inicio=?,fin=?,version=version+1,actualizado_por=? WHERE periodo=?',[$state,$start,$end,$u['id'],$period]);
 else q($d,'INSERT INTO imi_periodos(periodo,estado,inicio,fin,actualizado_por) VALUES(?,?,?,?,?)',[$period,$state,$start,$end,$u['id']]);
 $d->commit();$_SESSION['flash']=$action==='cerrar'?'Periodo cerrado. Se bloqueó el ingreso de información.':'Periodo habilitado con el plazo indicado.';go('periodos_imi.php?mes='.$month);
 }catch(Throwable $ex){if($d->inTransaction())$d->rollBack();if($ex instanceof RuntimeException&&!($ex instanceof PDOException))$error=$ex->getMessage();else{error_log($ex->getMessage());$error='No se pudo actualizar el periodo. Recargue e intente nuevamente.';}}
}
$window=q($d,'SELECT * FROM imi_periodos WHERE periodo=?',[$period])->fetch()?:null;
$all=q($d,'SELECT p.*,u.nombre responsable FROM imi_periodos p LEFT JOIN usuarios u ON u.id=p.actualizado_por ORDER BY p.periodo DESC')->fetchAll();
top('Periodos de ingreso IMI',$u);if($error)echo '<p class="alert">'.e($error).'</p>';if(isset($_SESSION['flash'])){echo '<p class="success">'.e($_SESSION['flash']).'</p>';unset($_SESSION['flash']);}
?>
<section class="card"><h2>Control exclusivo del administrador</h2><p>Elige el mes del IMI y habilita su ingreso para todos los establecimientos. Puedes establecer fecha y hora de inicio y fin, o dejar ambos campos vacíos para abrirlo inmediatamente hasta que lo cierres.</p><p>Las fechas se interpretan en <strong>hora de Lima (Perú)</strong>. El plazo de ingreso puede ser distinto del mes reportado. Si el mes no está habilitado, solo admite consulta.</p><form method="get" class="filters"><label>Mes reportado<input type="month" name="mes" min="2000-01" max="2099-12" value="<?=e($month)?>" required></label><button>Seleccionar mes</button></form></section>
<section class="card"><h2><?=e($month)?> · <?=e(imi_periodo_texto($window))?></h2><form method="post" class="grid"><?=token()?><input type="hidden" name="mes" value="<?=e($month)?>"><input type="hidden" name="version" value="<?=e($window['version']??0)?>"><label>Inicio de ingreso (opcional)<input type="datetime-local" name="inicio" value="<?=e(($window['inicio']??null)?substr(str_replace(' ','T',$window['inicio']),0,16):'')?>"></label><label>Fin de ingreso (opcional)<input type="datetime-local" name="fin" value="<?=e(($window['fin']??null)?substr(str_replace(' ','T',$window['fin']),0,16):'')?>"></label><div><button name="action" value="abrir">Abrir / actualizar plazo</button> <button class="danger" name="action" value="cerrar" formnovalidate>Cerrar ingreso ahora</button></div></form><p class="muted">Cerrar bloquea guardar borradores, enviar, cambiar apertura y reabrir informes, incluso con una página que quedó abierta. Los IMI enviados siguen disponibles para consulta y exportación según los permisos de cada cuenta.</p><p>Los borradores no se eliminan ni se convierten automáticamente en enviados. El administrador los puede revisar sin modificar; los demás usuarios solo consultan informes enviados mientras el mes esté cerrado. Para corregir, abre el periodo y, si corresponde, reabre el último informe enviado.</p><a href="imi.php?mes=<?=e($month)?>">Ir a IMI →</a></section>
<section class="card table"><table><thead><tr><th>Mes del IMI</th><th>Estado efectivo</th><th>Inicio · Lima</th><th>Fin · Lima</th><th>Último cambio por</th><th>Acción</th></tr></thead><tbody><?php foreach($all as $p):?><tr><td><?=e(substr($p['periodo'],0,7))?></td><td><?=e(imi_periodo_texto($p))?></td><td><?=e($p['inicio']??'Inmediato')?></td><td><?=e($p['fin']??'Sin fecha límite')?></td><td><?=e($p['responsable']??'Cuenta eliminada')?></td><td><a href="periodos_imi.php?mes=<?=e(substr($p['periodo'],0,7))?>">Administrar</a></td></tr><?php endforeach;?></tbody></table></section><?php bottom();
