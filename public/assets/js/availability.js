(()=>{
 const form=document.querySelector('[data-price-form][data-availability-url]');
 if(!form)return;
 const hint=form.querySelector('[data-availability-state]');
 const input=form.elements.event_date_jalali, select=form.elements.time_slot;
 if(!hint||!input||!select)return;
 let call=0;
 async function check(){
  const id=++call, date=input.value;
  if(!date){hint.textContent='ابتدا تاریخ را انتخاب کنید.';return;}
  hint.textContent='در حال بررسی ظرفیت سانس…';
  try{
   const url=new URL(form.dataset.availabilityUrl,location.href);
   url.searchParams.set('date',date);
   if(form.dataset.reservationId)url.searchParams.set('reservation_id',form.dataset.reservationId);
   const res=await fetch(url,{headers:{Accept:'application/json'},cache:'no-store'});
   const data=await res.json();if(!res.ok)throw Error('خطا در بررسی ظرفیت');
   if(id!==call)return;
   for(const option of select.options)option.disabled=!data[option.value];
   if(select.selectedOptions[0]?.disabled){
    const available=[...select.options].find(o=>!o.disabled);
    if(available){select.value=available.value;select.dispatchEvent(new Event('change',{bubbles:true}));}
   }
   if(!data.day&&!data.night)hint.textContent='این روز کاملاً رزرو شده است؛ تاریخ دیگری انتخاب کنید.';
   else hint.textContent='وضعیت سانس: روز '+(data.day?'آزاد':'پر')+' | شب '+(data.night?'آزاد':'پر');
  }catch(e){if(id===call)hint.textContent='وضعیت آنلاین در دسترس نیست؛ زمان ثبت دوباره بررسی می‌شود.'}
 }
 input.addEventListener('change',check);
 if(input.value)check();
})();
