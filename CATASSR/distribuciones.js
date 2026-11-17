'use strict';
const distRows=Array.from(document.querySelectorAll('[data-dist-row]'));
const search=document.querySelector('#dist-search'),priority=document.querySelector('#dist-priority');
const normalize=s=>s.normalize('NFD').replace(/\p{M}/gu,'').toLowerCase();
function distFilter(){const q=normalize(search?.value||'');for(const row of distRows)row.hidden=!normalize(row.dataset.search).includes(q)||(!!priority?.value&&row.dataset.state!==priority.value);}
search?.addEventListener('input',distFilter);priority?.addEventListener('change',distFilter);
const form=document.querySelector('#dist-form'),stock=document.querySelector('#dist-stock');
function distTotals(){if(!stock)return;const inputs=Array.from(form?.querySelectorAll('[name^="cantidades["]')||[]);if(!inputs.length)return;const sum=inputs.reduce((n,x)=>n+Number(x.value||0),0),balance=Number(stock.dataset.stock)-sum;document.querySelector('#dist-total').textContent=sum.toLocaleString('es-PE');const out=document.querySelector('#dist-balance');out.textContent=balance.toLocaleString('es-PE');out.classList.toggle('dist-negative',balance<0);}
form?.addEventListener('input',distTotals);
document.querySelector('#dist-fill')?.addEventListener('click',()=>{for(const row of distRows){if(row.hidden)continue;const input=row.querySelector('[data-suggested]');if(input&&input.dataset.suggested!=='')input.value=input.dataset.suggested;}distTotals();});distTotals();
// Avoid closing a programming with quantities edited on screen but not saved.
let distDirty=false;
form?.addEventListener('input',()=>{distDirty=true;});
document.querySelector('#dist-fill')?.addEventListener('click',()=>{distDirty=true;});
form?.addEventListener('submit',()=>{distDirty=false;});
document.addEventListener('submit',event=>{
 const action=event.target.querySelector('[name="action"]')?.value;
 if(distDirty&&['cerrar','actualizar'].includes(action)){event.preventDefault();alert('Guarda primero el borrador del producto para conservar las cantidades que editaste.');}
});
window.addEventListener('beforeunload',event=>{if(distDirty){event.preventDefault();event.returnValue='';}});
