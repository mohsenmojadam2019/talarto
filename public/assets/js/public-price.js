(()=>{
 const form=document.querySelector('[data-public-calculator]');if(!form)return;
 const state=document.querySelector('[data-public-price-state]'),lines=document.querySelector('[data-public-price-lines]'),result=document.querySelector('[data-public-price-result]');
 const fmt=n=>new Intl.NumberFormat('fa-IR').format(Number(n||0));
 let active;
 async function run(){
  if(!form.elements.event_date_jalali.value||!form.elements.package_id.value){state.textContent='تاریخ و پکیج را انتخاب کنید.';result.textContent='—';return}
  active?.abort();active=new AbortController();state.textContent='در حال محاسبه…';
  try{
   const response=await fetch(form.dataset.previewUrl,{method:'POST',body:new FormData(form),headers:{Accept:'application/json'},signal:active.signal});
   const data=await response.json();
   if(!response.ok)throw Error(Object.values(data.errors||{})[0]?.[0]||data.message||'خطا در محاسبه');
   state.textContent='برآورد با موتور قیمت سمت سرور';
   lines.textContent='';
   for(const [label,key] of [['پایه پکیج','base_price'],['هزینه مهمان‌ها','guest_total'],['منو','menu_total'],['تعدیل تاریخ','date_adjustment_total']]){
    if(!Number(data[key]))continue;
    const row=document.createElement('p');row.textContent=label+' : '+fmt(data[key])+' تومان';lines.append(row);
   }
   result.textContent=fmt(data.final_price);
  }catch(e){if(e.name==='AbortError')return;state.textContent=e.message;result.textContent='—'}
 }
 form.addEventListener('submit',e=>{e.preventDefault();run()});
 form.addEventListener('change',run);
})();
