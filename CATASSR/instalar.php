<?php
require __DIR__.'/app.php';
$error='';$done=false;
try {$d=db();$done=(bool)q($d,'SELECT id FROM instalacion WHERE id=1')->fetch();}catch(PDOException $ex){}
if($done){top('CATASSR ya está instalado');echo '<section class="card"><p>La instalación está bloqueada para proteger los datos existentes.</p><a class="button" href="index.php">Iniciar sesión</a></section>';bottom();exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 csrf();$lock=fopen(__DIR__.'/.installation.lock','c');
 if(!$lock || !flock($lock,LOCK_EX|LOCK_NB)){http_response_code(409);exit('Hay otra instalación en curso.');}
 try{
 $adminPassword=(string)($_POST['admin_password']??'');
 if(strlen($adminPassword)<8)throw new RuntimeException('La contraseña debe tener al menos 8 caracteres.');
 $d=db(false);$name=$c['database'];if(!preg_match('/^[a-zA-Z0-9_]+$/',$name))throw new RuntimeException('Nombre de base inválido.');
 $d->exec('CREATE DATABASE IF NOT EXISTS `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');$d=db();
 foreach(explode(';',file_get_contents(__DIR__.'/schema.sql')) as $sql)if(trim($sql))$d->exec($sql);
 if(q($d,'SELECT id FROM instalacion WHERE id=1')->fetch())throw new RuntimeException('El sistema ya está instalado.');
 foreach(['usuarios','establecimientos','microredes','productos'] as $table)if(q($d,'SELECT COUNT(*) FROM '.$table)->fetchColumn())throw new RuntimeException('La base contiene datos. No se sobrescribirá. Use una base vacía.');
 $data=json_decode(file_get_contents(__DIR__.'/datos.json'),true,512,JSON_THROW_ON_ERROR);$d->beginTransaction();
 foreach($data['microredes'] as $i=>$n)q($d,'INSERT INTO microredes(id,nombre) VALUES(?,?)',[$i+1,$n]);
 foreach($data['establecimientos'] as $x)q($d,'INSERT INTO establecimientos(codigo,nombre,microred_id,cod_distrito,distrito,categoria) VALUES(?,?,?,?,?,?)',array_values($x));
 foreach($data['productos'] as $x)q($d,'INSERT INTO productos(codigo,nombre,tipo,unidad) VALUES(?,?,?,?)',array_values($x));
 q($d,'INSERT INTO usuarios(nombre,usuario,clave,rol) VALUES(?,?,?,?)',['VICTOR INFANTES','victor.infantes',password_hash($adminPassword,PASSWORD_DEFAULT),'admin']);q($d,'INSERT INTO instalacion(id) VALUES(1)');$d->commit();$done=true;
 }catch(Throwable $ex){if(isset($d)&&$d->inTransaction())$d->rollBack();error_log($ex->getMessage());$error='No se pudo instalar. Ingrese una contraseña de al menos 8 caracteres. Verifique config.php, que MySQL esté iniciado y que la base esté vacía. Detalle técnico en el registro de PHP/XAMPP.';}finally{flock($lock,LOCK_UN);fclose($lock);}
}
top('Bienvenido a CATASSR'); ?>
<section class="card login"><div class="eyebrow">INSTALACIÓN LOCAL · XAMPP</div><h2>Tu red de salud, conectada.</h2><p>Se cargarán 196 establecimientos, 11 microredes y el catálogo de tu Excel. Las cuentas de responsables se crearán paulatinamente.</p>
<?php if($error):?><p class="alert"><?=e($error)?></p><?php endif;?>
<?php if($done):?><p class="success">Instalación terminada.</p><p>Usuario: <strong>victor.infantes</strong><br>Contraseña: la que acabas de introducir durante la instalación.</p><a class="button" href="index.php">Ingresar al sistema</a><?php else:?><form method="post"><?=token()?><label>Contraseña del administrador<input type="password" name="admin_password" minlength="8" required autocomplete="new-password"></label><p class="muted">Usuario: victor.infantes. Elige la contraseña que utilizarás para ingresar.</p><button>Instalar CATASSR</button></form><?php endif;?></section><?php bottom();
