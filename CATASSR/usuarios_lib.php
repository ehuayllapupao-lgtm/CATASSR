<?php
function usuarios_mapa(): array {
 return ['Á'=>'A','á'=>'a','À'=>'A','à'=>'a','Â'=>'A','â'=>'a','Ä'=>'A','ä'=>'a','Ã'=>'A','ã'=>'a','Å'=>'A','å'=>'a','É'=>'E','é'=>'e','È'=>'E','è'=>'e','Ê'=>'E','ê'=>'e','Ë'=>'E','ë'=>'e','Í'=>'I','í'=>'i','Ì'=>'I','ì'=>'i','Î'=>'I','î'=>'i','Ï'=>'I','ï'=>'i','Ó'=>'O','ó'=>'o','Ò'=>'O','ò'=>'o','Ô'=>'O','ô'=>'o','Ö'=>'O','ö'=>'o','Õ'=>'O','õ'=>'o','Ø'=>'O','ø'=>'o','Ú'=>'U','ú'=>'u','Ù'=>'U','ù'=>'u','Û'=>'U','û'=>'u','Ü'=>'U','ü'=>'u','Ñ'=>'N','ñ'=>'n','Ç'=>'C','ç'=>'c','Æ'=>'AE','æ'=>'ae','Œ'=>'OE','œ'=>'oe','ß'=>'ss'];
}
function usuarios_letras(string $value): string {
 $value=strtr($value,usuarios_mapa());$value=preg_replace('/\p{M}/u','',$value)??'';
 return strtoupper(preg_replace('/[^A-Za-z]/','',$value));
}
function usuarios_nombre($raw,string $label): string {
 if(!is_string($raw))throw new RuntimeException('Complete '.$label.'.');
 $value=trim(preg_replace('/\s+/u',' ',$raw)??'');
 if($value===''||preg_match_all('/./us',$value)>100||!preg_match("/^[\\p{L}\\p{M} .’'\\-]+$/u",$value)||usuarios_letras($value)==='')throw new RuntimeException('Revise '.$label.': use letras y un máximo de 100 caracteres.');
 return $value;
}
function usuarios_identidad(array $input): array {
 $nombres=usuarios_nombre($input['nombres']??null,'los nombres');
 $paterno=usuarios_nombre($input['apellido_paterno']??null,'el apellido paterno');
 $materno=usuarios_nombre($input['apellido_materno']??null,'el apellido materno');
 $nombre=$nombres.' '.$paterno.' '.$materno;
 if(preg_match_all('/./us',$nombre)>150)throw new RuntimeException('El nombre completo es demasiado largo (máximo 150 caracteres).');
 $base=substr(usuarios_letras($nombres),0,1).usuarios_letras($paterno).substr(usuarios_letras($materno),0,1);
 // Reserve space for a numeric suffix if other accounts use the same identifier.
 if(strlen($base)>75)throw new RuntimeException('El apellido paterno es demasiado largo para generar el usuario.');
 return ['nombre'=>$nombre,'base'=>$base];
}
function usuarios_crear(PDO $d,array $identity,string $hash,string $role,?int $mid,?int $eid,int $active,array $contact=[]): string {
 for($i=0;$i<10000;$i++){
  $login=$identity['base'].($i===0?'':(string)$i);
  if(q($d,'SELECT id FROM usuarios WHERE usuario=?',[$login])->fetch())continue;
  try{q($d,'INSERT INTO usuarios(nombre,usuario,clave,rol,microred_id,establecimiento_id,activo,telefono_1,telefono_2,correo) VALUES(?,?,?,?,?,?,?,?,?,?)',[$identity['nombre'],$login,$hash,$role,$mid,$eid,$active,$contact['telefono_1']??null,$contact['telefono_2']??null,$contact['correo']??null]);return $login;}
  catch(PDOException $ex){
   // Retry only a username collision, including simultaneous submissions.
   if((int)($ex->errorInfo[1]??0)!==1062 || !q($d,'SELECT id FROM usuarios WHERE usuario=?',[$login])->fetch())throw $ex;
  }
 }
 throw new RuntimeException('No se pudo asignar un usuario disponible. Contacte al administrador.');
}
function usuarios_contacto_ready(PDO $d): bool {
 try{q($d,'SELECT telefono_1,telefono_2,correo FROM usuarios LIMIT 0');return true;}catch(PDOException $ex){return false;}
}
function usuarios_contacto(array $input,array $old=[]): array {
 $contact=[];
 foreach(['telefono_1'=>'Número telefónico 1','telefono_2'=>'Número telefónico 2','correo'=>'Correo'] as $key=>$label){
  $raw=$input[$key]??$old[$key]??'';
  if(!is_string($raw))throw new RuntimeException('Revise '.$label.'.');
  $value=trim($raw);if($value===''){$contact[$key]=null;continue;}
  if($key==='correo'){
   if(strlen($value)>254||!filter_var($value,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Ingrese un correo electrónico válido.');
  }else{
   $digits=preg_replace('/\D/','',$value);
   if(strlen($value)>30||!preg_match('/^\+?[0-9 ()-]+$/',$value)||strlen($digits)<7||strlen($digits)>15)throw new RuntimeException($label.': ingrese entre 7 y 15 dígitos; puede usar +, espacios, paréntesis o guiones.');
  }
  $contact[$key]=$value;
 }
 return $contact;
}
function usuarios_whatsapp_url(?string $phone): ?string {
 $phone=trim($phone??'');
 if($phone===''||strlen($phone)>30||!preg_match('/^\+?[0-9 ()-]+$/',$phone))return null;
 $digits=preg_replace('/\D/','',$phone);
 if(strncmp($phone,'+',1)===0){$international=$digits;}
 elseif(strncmp($digits,'00',2)===0){$international=substr($digits,2);}
 elseif(preg_match('/^9\d{8}$/',$digits)){$international='51'.$digits;}
 elseif(preg_match('/^0[1-8]\d{6,7}$/',$digits)){$international='51'.substr($digits,1);}
 elseif(preg_match('/^[1-8]\d{7}$/',$digits)){$international='51'.$digits;}
 elseif(preg_match('/^51\d{8,9}$/',$digits)){$international=$digits;}
 else return null;
 return preg_match('/^[1-9]\d{7,14}$/',$international)?'https://wa.me/'.$international:null;
}
