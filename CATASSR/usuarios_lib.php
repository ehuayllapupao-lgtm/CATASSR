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
function usuarios_crear(PDO $d,array $identity,string $hash,string $role,?int $mid,?int $eid,int $active): string {
 for($i=0;$i<10000;$i++){
  $login=$identity['base'].($i===0?'':(string)$i);
  if(q($d,'SELECT id FROM usuarios WHERE usuario=?',[$login])->fetch())continue;
  try{q($d,'INSERT INTO usuarios(nombre,usuario,clave,rol,microred_id,establecimiento_id,activo) VALUES(?,?,?,?,?,?,?)',[$identity['nombre'],$login,$hash,$role,$mid,$eid,$active]);return $login;}
  catch(PDOException $ex){
   // Retry only a username collision, including simultaneous submissions.
   if((int)($ex->errorInfo[1]??0)!==1062 || !q($d,'SELECT id FROM usuarios WHERE usuario=?',[$login])->fetch())throw $ex;
  }
 }
 throw new RuntimeException('No se pudo asignar un usuario disponible. Contacte al administrador.');
}
