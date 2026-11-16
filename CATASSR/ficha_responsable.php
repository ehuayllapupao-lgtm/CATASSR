<?php
require __DIR__.'/app.php';
$json=strpos($_SERVER['HTTP_ACCEPT']??'','application/json')!==false;
function ficha_responder(array $payload,int $status,?array $u=null): void {
 global $json;http_response_code($status);
 if($json){header('Content-Type: application/json; charset=UTF-8');echo json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);exit;}
 top('Ficha del responsable',$u);echo '<section class="card">';
 if(isset($payload['datos'])){echo '<dl class="ficha-datos">';foreach($payload['datos'] as $label=>$value)echo '<dt>'.e($label).'</dt><dd>'.e($value).'</dd>';echo '</dl>';}
 else echo '<p class="alert">'.e($payload['error']).'</p>';
 echo '<a class="button" href="index.php?page=establecimientos">Volver a establecimientos</a></section>';bottom();exit;
}
if($_SERVER['REQUEST_METHOD']!=='GET'){header('Allow: GET');ficha_responder(['error'=>'Método no permitido.'],405);}
try{
 $d=db();$u=isset($_SESSION['uid'])?q($d,'SELECT * FROM usuarios WHERE id=? AND activo=1',[$_SESSION['uid']])->fetch():null;
 if(!$u)ficha_responder(['error'=>'Inicie sesión para consultar la ficha.'],401);
 $id=$_GET['establecimiento_id']??'';
 if(!is_string($id)||!preg_match('/^[1-9]\d{0,8}$/',$id))ficha_responder(['error'=>'Establecimiento no válido.'],422,$u);
 [$scope,$args]=usuario_ambito($u);
 // Explicit public profile fields; no passwords, hashes, lock information or other accounts.
 $x=q($d,'SELECT e.codigo,e.nombre establecimiento,e.distrito,e.categoria,m.nombre microred,u.nombre responsable,u.usuario,u.rol,u.activo,u.microred_id microred_supervision_id,s.nombre microred_supervision FROM establecimientos e JOIN microredes m ON m.id=e.microred_id JOIN usuarios u ON u.establecimiento_id=e.id LEFT JOIN microredes s ON s.id=u.microred_id WHERE e.id=? AND '.$scope,array_merge([(int)$id],$args))->fetch();
 if(!$x)ficha_responder(['error'=>'No hay una ficha de responsable disponible para este establecimiento en su ámbito.'],404,$u);
 $data=['Nombre completo'=>$x['responsable'],'Usuario'=>$x['usuario'],'Funciones'=>usuario_rol_etiqueta(['rol'=>$x['rol'],'microred_id'=>$x['microred_supervision_id']]),'Estado de cuenta'=>$x['activo']?'Activa':'Inactiva','Establecimiento'=>$x['establecimiento'],'Código del establecimiento'=>$x['codigo'],'Microred del establecimiento'=>$x['microred'],'Distrito'=>$x['distrito'],'Categoría'=>$x['categoria']];
 if($x['rol']==='microred'||($x['rol']==='establecimiento'&&$x['microred_supervision_id']))$data['Microred que supervisa']=$x['microred_supervision']??'Sin asignar';
 ficha_responder(['datos'=>$data],200,$u);
}catch(Throwable $ex){error_log($ex->getMessage());ficha_responder(['error'=>'No se pudo cargar la ficha. Intente nuevamente.'],500,$u??null);}
