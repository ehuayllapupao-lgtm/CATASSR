'use strict';
const userForm=document.querySelector('[data-usuario-auto]');
if(userForm){
 const map=JSON.parse(userForm.dataset.mapa);
 const letters=value=>Array.from(value).map(c=>map[c]??c).join('').replace(/\p{M}/gu,'').replace(/[^A-Za-z]/g,'').toUpperCase();
 const field=name=>userForm.querySelector('[name="'+name+'"]');
 const update=()=>{
  const n=letters(field('nombres').value),p=letters(field('apellido_paterno').value),m=letters(field('apellido_materno').value);
  field('usuario').value=n&&p&&m?n.charAt(0)+p+m.charAt(0):'';
 };
 for(const name of ['nombres','apellido_paterno','apellido_materno'])field(name).addEventListener('input',update);
 update();
}
