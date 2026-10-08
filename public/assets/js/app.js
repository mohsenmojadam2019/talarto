(()=>{
 const $=(q,r=document)=>r.querySelector(q), $$=(q,r=document)=>[...r.querySelectorAll(q)];
 const mt=$('[data-menu-toggle]'), menu=$('[data-menu]'); if(mt&&menu)mt.addEventListener('click',()=>menu.classList.toggle('open'));
 const calc=$('[data-calculator]'); if(calc){const run=()=>{const o=$('[data-package]',calc).selectedOptions[0],g=Math.max(0,Number($('[data-guests]',calc).value||0)),m=Number($('[data-menu]',calc).value||0),base=Number(o.dataset.base||0),per=Number(o.dataset.per||0),total=base+(per*g)+(m*g);$('[data-calc-result]').textContent=total?new Intl.NumberFormat('fa-IR').format(total):'—'};calc.addEventListener('input',run);run();}
 const modal=$('[data-gallery-modal]'); if(modal){$$('[data-gallery-src]').forEach(b=>b.addEventListener('click',()=>{modal.querySelector('img').src=b.dataset.gallerySrc;modal.classList.add('open')}));$('[data-gallery-close]',modal)?.addEventListener('click',()=>modal.classList.remove('open'));modal.addEventListener('click',e=>{if(e.target===modal)modal.classList.remove('open')});}
 const tabs=$('[data-tabs]'); if(tabs){const activate=k=>{$$('[data-tab]',tabs).forEach(b=>b.classList.toggle('active',b.dataset.tab===k));$$('[data-panel]').forEach(p=>p.hidden=p.dataset.panel!==k);const overview=$('.mw-admin-dashboard');if(overview)overview.hidden=k!=='dashboard';const management=$('#mw-admin-management');if(management)management.hidden=k==='dashboard';location.hash='admin-'+k};$$('[data-tab]',tabs).forEach(b=>b.addEventListener('click',()=>activate(b.dataset.tab)));let first=(location.hash||(document.querySelector('.mw-admin')?'#admin-dashboard':'#admin-reservations')).replace('#admin-',''); if(!$(`[data-tab="${first}"]`,tabs))first='reservations';activate(first);window.addEventListener('hashchange',()=>{const next=location.hash.replace('#admin-','');if($(`[data-tab="${next}"]`,tabs))activate(next)});$$('[data-select-tab]').forEach(el=>el.addEventListener('click',e=>{e.preventDefault();activate(el.dataset.selectTab);$('html').scrollTo({top:0,behavior:'smooth'})}));}
 const faDigits=s=>String(s).replace(/\d/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]);
 const parts=d=>{const p=new Intl.DateTimeFormat('en-US-u-ca-persian',{year:'numeric',month:'numeric',day:'numeric'}).formatToParts(d);const o={};p.forEach(x=>{if(['year','month','day'].includes(x.type))o[x.type]=Number(x.value)});return o};
 const monthNames=['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
 const weeks=['ش','ی','د','س','چ','پ','ج'];
 // Index Jalali months by Gregorian Date objects; supports editing older admin rules too.
 const dateIndex=new Map(),months=[],monthSeen=new Set();
 const dateBase=new Date();dateBase.setHours(12,0,0,0);
 for(let i=-570;i<=1170;i++){
   const day=new Date(dateBase);day.setDate(dateBase.getDate()+i);
   const p=parts(day),k=p.year+'/'+p.month;
   if(!monthSeen.has(k)){monthSeen.add(k);months.push({year:p.year,month:p.month})}
   if(!dateIndex.has(k))dateIndex.set(k,[]);
   dateIndex.get(k).push({date:day,day:p.day});
 }
 function daysFor(y,m){return dateIndex.get(y+'/'+m)||[]}
 function defaultMonth(input){
  const digits=String(input?.value||'').replace(/[۰-۹]/g,x=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(x)).replace(/[٠-٩]/g,x=>'٠١٢٣٤٥٦٧٨٩'.indexOf(x));
  const chosen=digits.match(/^(1[34]\d{2})\/(0?[1-9]|1[0-2])\//),today=parts(new Date());
  const y=chosen?Number(chosen[1]):today.year,m=chosen?Number(chosen[2]):today.month;
  return Math.max(0,months.findIndex(item=>item.year===y&&item.month===m));
 }
 function key(y,m,d){return `${y}/${String(m).padStart(2,'0')}/${String(d).padStart(2,'0')}`}
 let picker,currentInput,monthIndex=0;
 function closePicker(){picker?.classList.remove('open')}
 function render(){if(!picker||!currentInput)return;const ym=months[monthIndex],days=daysFor(ym.year,ym.month),blocked=new Set(JSON.parse(currentInput.dataset.blocked||'[]'));$('.jp-title',picker).textContent=monthNames[ym.month-1]+' '+faDigits(ym.year);const grid=$('.jp-grid',picker);grid.innerHTML='';if(!days.length)return;let offset=(days[0].date.getDay()+1)%7;for(let i=0;i<offset;i++)grid.insertAdjacentHTML('beforeend','<span></span>');const today=parts(new Date());days.forEach(it=>{const k=key(ym.year,ym.month,it.day),b=document.createElement('button');b.type='button';b.className='jp-day'+(blocked.has(k)?' blocked':'')+(today.year===ym.year&&today.month===ym.month&&today.day===it.day?' today':'');b.textContent=faDigits(it.day);b.disabled=blocked.has(k)||(!currentInput.hasAttribute('data-allow-past')&&it.date<new Date(new Date().setHours(0,0,0,0)));b.addEventListener('click',()=>{currentInput.value=faDigits(k);currentInput.dispatchEvent(new Event('change',{bubbles:true}));closePicker()});grid.appendChild(b)});$('.jp-prev',picker).disabled=monthIndex===0;$('.jp-next',picker).disabled=monthIndex===months.length-1;}
 function ensurePicker(){if(picker)return;picker=document.createElement('div');picker.className='jalali-picker';picker.innerHTML=`<div class="jp-head"><button type="button" class="jp-prev">›</button><div class="jp-title"></div><button type="button" class="jp-next">‹</button></div><div class="jp-week">${weeks.map(w=>`<span>${w}</span>`).join('')}</div><div class="jp-grid"></div>`;document.body.appendChild(picker);$('.jp-prev',picker).addEventListener('click',()=>{if(monthIndex>0){monthIndex--;render()}});$('.jp-next',picker).addEventListener('click',()=>{if(monthIndex<months.length-1){monthIndex++;render()}});}
 $$('[data-jalali-picker]').forEach(inp=>inp.addEventListener('click',e=>{e.stopPropagation();ensurePicker();currentInput=inp;monthIndex=defaultMonth(inp);const r=inp.getBoundingClientRect();picker.style.top=Math.min(innerHeight-440,r.bottom+8)+'px';picker.style.right=Math.max(12,innerWidth-r.right)+'px';picker.classList.add('open');render()}));document.addEventListener('click',e=>{if(picker&&!picker.contains(e.target)&&e.target!==currentInput)closePicker()});
})();
