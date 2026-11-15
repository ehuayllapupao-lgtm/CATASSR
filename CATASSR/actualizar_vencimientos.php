<?php
require __DIR__.'/app.php';require __DIR__.'/vencimientos_lib.php';
try{$d=db();$u=isset($_SESSION['uid'])?q($d,'SELECT * FROM usuarios WHERE id=? AND activo=1',[$_SESSION['uid']])->fetch():null;}catch(PDOException $ex){go('index.php');}
if(!$u)go('index.php');if($u['rol']!=='admin'){http_response_code(403);exit('Solo el administrador puede habilitar las fechas de vencimiento.');}
$error='';if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf();try{
 if(!vencimientos_ready($d)){
  try{$d->exec('ALTER TABLE productos ADD COLUMN fecha_vencimiento DATE NULL DEFAULT NULL');}
  catch(PDOException $ex){if((int)($ex->errorInfo[1]??0)!==1060)throw $ex;}
 }
 if(!vencimientos_ready($d))throw new RuntimeException('Columna de vencimiento no disponible.');
 $_SESSION['flash']='Fechas de vencimiento habilitadas. Los productos existentes se conservaron sin fechas inventadas.';go('index.php?page=productos');
 }catch(Throwable $ex){error_log($ex->getMessage());$error='No se pudo actualizar. Revise los permisos MySQL e intente nuevamente; no reinstale CATASSR.';}
}
top('Habilitar fechas de vencimiento',$u);if($error)echo '<p class="alert">'.e($error).'</p>';?><section class="card"><h2>Medicamentos e insumos</h2><p>Se agrega una fecha de vencimiento opcional por producto del catálogo. Los registros existentes conservarán sus datos y aparecerán como “Sin registrar” hasta que el administrador ingrese la fecha correspondiente.</p><form method="post"><?=token()?><button>Habilitar fechas de vencimiento</button></form><p>No es necesario reinstalar ni importar la base de datos.</p></section><?php bottom();
