<?php
require __DIR__.'/app.php';require __DIR__.'/usuarios_lib.php';
$json=strpos($_SERVER['HTTP_ACCEPT']??'','application/json')!==false;
function ficha_responder(array $payload,int $status,?array $u=null): void {
 global $json;http_response_code($status);
 if($json){header('Content-Type: application/json; charset=UTF-8');echo json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);exit;}
 top('Ficha del responsable',$u);echo '<section class="card">';
 if(isset($payload['datos'])){
  echo '<dl class="ficha-datos">';
  foreach($payload['datos'] as $label=>$value){
   echo '<dt>'.e($label).'</dt><dd><span class="ficha-contacto"><span>'.e($value).'</span>';
   if(!empty($payload['whatsapp'][$label]))echo '<a class="ficha-wa" href="'.e($payload['whatsapp'][$label]).'" target="_blank" rel="noopener noreferrer" title="Abrir WhatsApp para '.e($value).'" aria-label="Abrir chat de WhatsApp para '.e($label.': '.$value).'"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" focusable="false"><path d="M21 11.5a9 9 0 0 1-13.4 7.9L3 21l1.6-4.6A9 9 0 1 1 21 11.5Z"/><path d="m8 7 2 3-1.2 1.2c.8 1.5 2 2.7 3.5 3.5l1.2-1.2 3 2c-1.2 3.6-10.6-1.5-10-6.8C6.6 7.9 7.2 7.3 8 7Z"/></svg></a>';
   echo '</span></dd>';
  }echo '</dl>';
 }
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
 $contactSelect=usuarios_contacto_ready($d)?'u.telefono_1,u.telefono_2,u.correo,':'NULL telefono_1,NULL telefono_2,NULL correo,';
 // Explicit public profile fields; no passwords, hashes, lock information or other accounts.
 $x=q($d,'SELECT '.$contactSelect.'e.codigo,e.nombre establecimiento,e.distrito,e.categoria,m.nombre microred,u.nombre responsable,u.usuario,u.rol,u.activo,u.microred_id microred_supervision_id,s.nombre microred_supervision FROM establecimientos e JOIN microredes m ON m.id=e.microred_id JOIN usuarios u ON u.establecimiento_id=e.id LEFT JOIN microredes s ON s.id=u.microred_id WHERE e.id=? AND '.$scope,array_merge([(int)$id],$args))->fetch();
 if(!$x)ficha_responder(['error'=>'No hay una ficha de responsable disponible para este establecimiento en su ámbito.'],404,$u);
 $data=['Nombre completo'=>$x['responsable'],'Usuario'=>$x['usuario'],'Número telefónico 1'=>$x['telefono_1']??'Sin registrar','Número telefónico 2'=>$x['telefono_2']??'Sin registrar','Correo'=>$x['correo']??'Sin registrar','Funciones'=>usuario_rol_etiqueta(['rol'=>$x['rol'],'microred_id'=>$x['microred_supervision_id']]),'Estado de cuenta'=>$x['activo']?'Activa':'Inactiva','Establecimiento'=>$x['establecimiento'],'Código del establecimiento'=>$x['codigo'],'Microred del establecimiento'=>$x['microred'],'Distrito'=>$x['distrito'],'Categoría'=>$x['categoria']];
 if($x['rol']==='microred'||($x['rol']==='establecimiento'&&$x['microred_supervision_id']))$data['Microred que supervisa']=$x['microred_supervision']??'Sin asignar';
 $whatsapp=[];
 foreach(['Número telefónico 1'=>$x['telefono_1'],'Número telefónico 2'=>$x['telefono_2']] as $label=>$phone){$url=usuarios_whatsapp_url($phone);if($url)$whatsapp[$label]=$url;}
 ficha_responder(['datos'=>$data,'whatsapp'=>$whatsapp],200,$u);
}catch(Throwable $ex){error_log($ex->getMessage());ficha_responder(['error'=>'No se pudo cargar la ficha. Intente nuevamente.'],500,$u??null);}
