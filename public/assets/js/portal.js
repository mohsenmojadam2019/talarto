(()=>{
 const form=document.querySelector('[data-price-form]'); if(!form)return;
 const state=document.querySelector('[data-price-state]'),box=document.querySelector('[data-price-breakdown]'),total=document.querySelector('[data-price-total]');
 const money=n=>new Intl.NumberFormat('fa-IR').format(Math.abs(Number(n||0)));
 let timer,controller;
 async function preview(){
  const date=form.querySelector('[name="event_date_jalali"]')?.value,pack=form.querySelector('[name="package_id"]:checked')?.value||form.querySelector('[name="package_id"]')?.value;
  if(!date||!pack){state.textContent='تاریخ و پکیج را انتخاب کنید.';box.hidden=true;total.textContent='—';return}
  controller?.abort();controller=new AbortController();state.textContent='در حال محاسبه…';state.classList.add('loading');
  try{
   const data=new FormData(form);data.delete('_method');
   const res=await fetch(form.dataset.previewUrl,{method:'POST',body:data,headers:{'Accept':'application/json'},signal:controller.signal});
   const json=await res.json();if(!res.ok)throw new Error(json.message||Object.values(json.errors||{})?.[0]?.[0]||'خطا در محاسبه');
   state.textContent='قیمت بر اساس انتخاب فعلی';state.classList.remove('loading');box.innerHTML='';
   const rows=[['پایه پکیج',json.base_price],['هزینه مهمان',json.guest_total],['منو',json.menu_total],['خدمات اضافه',json.addon_total],['تعدیل تاریخ/سانس',json.date_adjustment_total]];
   rows.forEach(([label,value])=>{if(Number(value)!==0){const r=document.createElement('div');r.innerHTML=`<span>${label}</span><b class="${Number(value)<0?'negative':''}">${Number(value)<0?'− ':''}${money(value)}</b>`;box.appendChild(r)}});box.hidden=false;total.textContent=money(json.final_price);
  }catch(e){if(e.name==='AbortError')return;state.classList.remove('loading');state.textContent=e.message||'محاسبه قیمت انجام نشد.';box.hidden=true;total.textContent='—'}
 }
 function schedule(){clearTimeout(timer);timer=setTimeout(preview,260)}
 form.addEventListener('input',schedule);form.addEventListener('change',schedule);preview();
})();
