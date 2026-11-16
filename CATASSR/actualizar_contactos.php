<?php
require __DIR__.'/app.php';require __DIR__.'/usuarios_lib.php';
try{$d=db();$u=isset($_SESSION['uid'])?q($d,'SELECT * FROM usuarios WHERE id=? AND activo=1',[$_SESSION['uid']])->fetch():null;}catch(PDOException $ex){go('index.php');}
if(!$u)go('index.php');if($u['rol']!=='admin'){http_response_code(403);exit('Solo el administrador puede habilitar los datos de contacto.');}
$error='';if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf();try{
 foreach(['telefono_1'=>'VARCHAR(30)','telefono_2'=>'VARCHAR(30)','correo'=>'VARCHAR(254)'] as $column=>$type){
  if(q($d,'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=\'usuarios\' AND COLUMN_NAME=?',[$column])->fetchColumn())continue;
  try{$d->exec('ALTER TABLE usuarios ADD COLUMN '.$column.' '.$type.' NULL DEFAULT NULL');}
  catch(PDOException $ex){if((int)($ex->errorInfo[1]??0)!==1060)throw $ex;}
 }
 if(!usuarios_contacto_ready($d))throw new RuntimeException('No se habilitaron todos los campos.');
 $_SESSION['flash']='Datos de contacto habilitados. Las cuentas y sus accesos se conservaron.';go('index.php?page=usuarios');
 }catch(Throwable $ex){error_log($ex->getMessage());$error='No se pudo actualizar. Revise los permisos MySQL y vuelva a intentarlo; no reinstale CATASSR.';}
}
top('Habilitar teléfonos y correo',$u);if($error)echo '<p class="alert">'.e($error).'</p>';?><section class="card"><h2>Datos de contacto de usuarios</h2><p>Se agregan Número telefónico 1, Número telefónico 2 y Correo como campos opcionales. Las cuentas actuales conservarán sus datos, usuarios, contraseñas y asignaciones. Los contactos existentes no se sobrescriben al repetir la actualización.</p><form method="post"><?=token()?><button>Habilitar teléfonos y correo</button></form></section><?php bottom();
