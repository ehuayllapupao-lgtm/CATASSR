'use strict';
const searchForm=document.querySelector('#est-search-form');
if(searchForm){
 const input=searchForm.querySelector('[name="buscar"]'),micro=searchForm.querySelector('[name="microred"]');
 const suggestions=document.querySelector('#est-suggestions'),counter=document.querySelector('#est-result-count'),empty=document.querySelector('#est-empty');
 const normalize=value=>value.normalize('NFD').replace(/\p{M}/gu,'').toLowerCase();
 const rows=Array.from(document.querySelectorAll('#est-table tr[data-est]'));
 const update=()=>{
  const query=normalize(input.value.trim());let count=0;const choices=new Map();
  const add=(value,label)=>{if(value&&!choices.has(value)&&normalize(value).includes(query))choices.set(value,label);};
  for(const row of rows){
   const d=row.dataset,scope=!micro.value||d.microred===micro.value;
   const matches=scope&&normalize(d.name+' '+d.code+' '+d.district).includes(query);
   row.hidden=!matches;if(matches)count++;
   if(scope){add(d.name,d.code+' · '+d.district);add(d.code,d.name);add(d.district,'Distrito');}
  }
  counter.textContent=count+' establecimientos encontrados.';empty.hidden=count!==0;
  suggestions.replaceChildren();for(const [value,label] of Array.from(choices).slice(0,24)){const option=document.createElement('option');option.value=value;option.label=label;suggestions.append(option);}
 };
 input.addEventListener('input',update);micro.addEventListener('change',update);update();
}
const fichaDialog=document.querySelector('#responsable-dialog');
if(fichaDialog){
 const status=document.querySelector('#ficha-status'),data=document.querySelector('#ficha-data');let controller=null;
 fichaDialog.addEventListener('close',()=>{if(controller)controller.abort();});
 fichaDialog.addEventListener('click',event=>{if(event.target===fichaDialog){const r=fichaDialog.getBoundingClientRect();if(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom)fichaDialog.close();}});
 document.addEventListener('click',async event=>{
  const link=event.target.closest('a.ver-ficha');if(!link||event.ctrlKey||event.metaKey||event.shiftKey||event.altKey)return;
  if(typeof fichaDialog.showModal!=='function')return;
  event.preventDefault();if(controller)controller.abort();controller=new AbortController();const request=controller;
  data.replaceChildren();status.textContent='Cargando ficha…';if(!fichaDialog.open)fichaDialog.showModal();
  try{
   const response=await fetch(link.href,{headers:{Accept:'application/json'},credentials:'same-origin',signal:request.signal,cache:'no-store'});
   const payload=await response.json();if(request.signal.aborted)return;
   if(!response.ok){const error=new Error(payload.error||'No se pudo cargar la ficha.');error.userMessage=true;throw error;}
   for(const [label,value] of Object.entries(payload.datos)){
    const dt=document.createElement('dt'),dd=document.createElement('dd'),contact=document.createElement('span'),text=document.createElement('span');
    dt.textContent=label;contact.className='ficha-contacto';text.textContent=value;contact.append(text);
    const wa=payload.whatsapp?.[label];
    if(typeof wa==='string'&&/^https:\/\/wa\.me\/[1-9]\d{7,14}$/.test(wa)){
     const link=document.createElement('a');link.href=wa;link.className='ficha-wa';link.target='_blank';link.rel='noopener noreferrer';link.title='Abrir WhatsApp para '+value;link.setAttribute('aria-label','Abrir chat de WhatsApp para '+label+': '+value);
     const svg=document.createElementNS('http://www.w3.org/2000/svg','svg');
     for(const [attr,v] of Object.entries({viewBox:'0 0 24 24',width:'20',height:'20',fill:'none',stroke:'currentColor','stroke-width':'1.8','aria-hidden':'true',focusable:'false'}))svg.setAttribute(attr,v);
     for(const d of ['M21 11.5a9 9 0 0 1-13.4 7.9L3 21l1.6-4.6A9 9 0 1 1 21 11.5Z','m8 7 2 3-1.2 1.2c.8 1.5 2 2.7 3.5 3.5l1.2-1.2 3 2c-1.2 3.6-10.6-1.5-10-6.8C6.6 7.9 7.2 7.3 8 7Z']){const path=document.createElementNS('http://www.w3.org/2000/svg','path');path.setAttribute('d',d);svg.append(path);}
     link.append(svg);contact.append(link);
    }
    dd.append(contact);data.append(dt,dd);
   }
   status.textContent='Datos registrados del responsable.';
  }catch(error){if(error.name!=='AbortError'&&!request.signal.aborted)status.textContent=error.userMessage?error.message:'No se pudo cargar la ficha. Intente nuevamente.';}
 });
}
