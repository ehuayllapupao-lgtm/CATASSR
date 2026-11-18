'use strict';
(() => {
 const dialog=document.querySelector('#prioridades-inicio');
 if(!dialog||typeof dialog.showModal!=='function')return;
 const close=()=>{if(dialog.open)dialog.close();document.body.classList.remove('pi-modal-open');};
 dialog.querySelector('[data-pi-close]')?.addEventListener('click',close);
 dialog.addEventListener('close',()=>document.body.classList.remove('pi-modal-open'));
 dialog.addEventListener('click',event=>{
  if(event.target!==dialog)return;
  const r=dialog.getBoundingClientRect();
  if(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom)close();
 });
 // Una página restaurada desde el historial no debe repetir el aviso de ingreso.
 window.addEventListener('pagehide',close);
 window.addEventListener('pageshow',event=>{if(event.persisted)close();});
 dialog.showModal();
 document.body.classList.add('pi-modal-open');
})();
