<?php
declare(strict_types=1);
ini_set('session.use_strict_mode','1');
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax']);
session_start();
header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: DENY'); header('Cache-Control: no-store');
$c=require __DIR__.'/config.php';
function db(bool $database=true): PDO {global $c; return new PDO('mysql:host='.$c['host'].';port='.$c['port'].($database?';dbname='.$c['database']:'').';charset=utf8mb4',$c['user'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}
function q(PDO $db,string $sql,array $args=[]): PDOStatement {$s=$db->prepare($sql);$s->execute($args);return $s;}
function e($s): string {return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function go(string $url='index.php'): void {header('Location: '.$url);exit;}
function token(): string {if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));return '<input type="hidden" name="csrf" value="'.e($_SESSION['csrf']).'">';}
function csrf(): void {if(!hash_equals($_SESSION['csrf']??'',(string)($_POST['csrf']??'')) || empty($_SESSION['csrf'])) {http_response_code(403);exit('Solicitud vencida. Recargue la página.');}}
$roles=['admin'=>'Administrador','tecnico'=>'Equipo técnico','microred'=>'Cabecera de microred','establecimiento'=>'Encargada/o de establecimiento'];
function top(string $title,?array $u=null): void {global $roles; ?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · CATASSR</title><link rel="stylesheet" href="style.css"></head><body>
<?php if($u): ?><aside><a class="brand" href="index.php">✚ CATASSR</a><div class="profile"><div class="avatar"><?=e(strtoupper(substr($u['nombre'],0,1)))?></div><strong><?=e($u['nombre'])?></strong><small><?=e($roles[$u['rol']])?></small></div><nav><a href="index.php">▦ &nbsp; Panel principal</a><a href="index.php?page=microredes">◎ &nbsp; Microredes</a><a href="index.php?page=establecimientos">✚ &nbsp; Establecimientos</a><a href="index.php?page=productos">▤ &nbsp; Medicamentos e insumos</a><a href="imi.php">▤ &nbsp; IMI</a><?php if($u['rol']==='admin'):?><a href="periodos_imi.php">◷ &nbsp; Periodos IMI</a><a href="index.php?page=usuarios">♙ &nbsp; Usuarios y accesos</a><?php endif;?></nav><form method="post" action="index.php"><input type="hidden" name="action" value="logout"><?=token()?><button class="exit">Cerrar sesión</button></form><small class="aside-foot">Salud sexual y reproductiva<br>Gestión de establecimientos</small></aside><?php endif;?>
<main class="<?=$u?'':'guest'?>"><?php if($u):?><header><span>RED DE SALUD · GESTIÓN INSTITUCIONAL</span><span class="pill"><?=e($roles[$u['rol']])?></span></header><?php endif;?><div class="content"><h1><?=e($title)?></h1>
<?php }
function bottom(): void {echo '</div><footer>CATASSR · Administración e informes mensuales</footer></main></body></html>';}
