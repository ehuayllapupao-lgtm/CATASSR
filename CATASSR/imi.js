'use strict';
for(const row of document.querySelectorAll('.imi-table tbody tr')){
 const update=()=>{
  const val=k=>Number(row.querySelector('[data-key="'+k+'"]').value||0);
  const history=JSON.parse(row.dataset.history), total=Number(row.dataset.initial)+val('recibido');
  const final=total-val('consumo')-val('ajuste');
  const cpm=(val('consumo')+history.reduce((s,n)=>s+Number(n),0))/(history.length+1);
  const months=cpm>0?final/cpm:null;
  const request=stock=>cpm>0?Math.max(0,Math.ceil(Number((cpm*3-stock).toFixed(8)))):null;
  const state=final<0?'REVISAR SALDO':cpm<=0?'SIN CONSUMO PROMEDIO':final===0?'SIN EXISTENCIAS':months<1?'COBERTURA BAJA':months>6?'SOBRESTOCK':'COBERTURA ADECUADA';
  const data={total,final,cpm,meses:months,solicitar:request(final),segun:request(val('stock_segun')),estado:state};
  for(const [key,n] of Object.entries(data)){const cell=row.querySelector('[data-result="'+key+'"]');cell.textContent=n===null?'Sin base de consumo':typeof n==='number'?(Number.isInteger(n)?String(n):n.toFixed(2)):n;}
  row.classList.toggle('imi-error',final<0);
 };
 for(const input of row.querySelectorAll('input'))input.addEventListener('input',update);
}
const form=document.querySelector('#imi-form');
if(form)form.addEventListener('submit',event=>{if(document.querySelector('.imi-error')){event.preventDefault();alert('Corrija las filas con saldo negativo antes de guardar.');}});
