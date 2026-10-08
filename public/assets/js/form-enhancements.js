(()=>{
 "use strict";
 const $$=(q,r=document)=>Array.from(r.querySelectorAll(q));
 // Accessible input enhancements, without retaining sensitive form values.
 $$('input[type="password"]').forEach(input=>{
   if(input.closest('.password-control'))return;
   const wrapper=document.createElement('span');wrapper.className='password-control';
   input.parentNode.insertBefore(wrapper,input);wrapper.appendChild(input);
   const button=document.createElement('button');button.type='button';button.className='password-visibility';
   button.textContent='نمایش';button.setAttribute('aria-label','نمایش رمز عبور');
   button.setAttribute('aria-pressed','false');wrapper.appendChild(button);
   button.addEventListener('click',()=>{
     const reveal=input.type==='password';input.type=reveal?'text':'password';
     button.textContent=reveal?'مخفی':'نمایش';button.setAttribute('aria-pressed',String(reveal));
   });
 });
 const convertDigits=s=>String(s).replace(/[۰-۹]/g,v=>String('۰۱۲۳۴۵۶۷۸۹'.indexOf(v))).replace(/[٠-٩]/g,v=>String('٠١٢٣٤٥٦٧٨٩'.indexOf(v)));
 $$('input[name="mobile"],input[name="guest_count"],input[name="child_count"]').forEach(input=>{
    input.addEventListener('blur',()=>{input.value=convertDigits(input.value).trim()});
 });
 $$('textarea[maxlength]').forEach(field=>{
   const cap=Number(field.maxLength);if(!cap||cap<=0)return;
   const tip=document.createElement('span');tip.className='form-counter';field.insertAdjacentElement('afterend',tip);
   const update=()=>{tip.textContent=field.value.length+' / '+cap;tip.style.color=field.value.length>=cap?'#a14b26':''};
   field.addEventListener('input',update);update();
 });
 // Allow optional price-rule dates to be cleared without ever exposing native Gregorian pickers.
 $$('input[data-jalali-picker][data-allow-past]').forEach(input=>{
   const wrap=document.createElement('span');wrap.className='date-input-wrap';
   input.parentNode.insertBefore(wrap,input);wrap.appendChild(input);
   const clear=document.createElement('button');clear.type='button';clear.className='date-clear';
   clear.textContent='×';clear.title='پاک‌کردن تاریخ';clear.setAttribute('aria-label','پاک‌کردن تاریخ شمسی');
   wrap.appendChild(clear);
   clear.addEventListener('click',()=>{input.value='';input.dispatchEvent(new Event('change',{bubbles:true}))});
 });
 // Booking form navigation provides fast access to sections on a small screen.
 const booking=document.querySelector('.booking-form');
 if(booking){
   const sections=$$('.form-section',booking);
   if(sections.length>1){
     const nav=document.createElement('nav');nav.className='booking-steps';nav.setAttribute('aria-label','مراحل تنظیم مراسم');
     sections.forEach((section,i)=>{
       section.id=section.id||'booking-step-'+(i+1);
       const title=section.querySelector('h2')?.textContent.trim()||'مرحله '+(i+1);
       const a=document.createElement('a');a.href='#'+section.id;
       const mark=document.createElement('b');mark.textContent=String(i+1);
       a.append(mark,document.createTextNode(title));nav.appendChild(a);
     });
     booking.prepend(nav);
   }
 }
 // Search is client-side only; nothing about visitors or customers is sent to an external service.
 function attachFilter(input,items,getText){
   if(!input||!items.length)return;
   let note=document.createElement('div');note.className='search-result-count';note.setAttribute('aria-live','polite');
   input.closest('label')?.insertAdjacentElement('afterend',note);
   const update=()=>{
     const q=convertDigits(input.value).trim().toLocaleLowerCase('fa');
     let n=0;
     for(const item of items){
       const match=!q||convertDigits(getText(item)).toLocaleLowerCase('fa').includes(q);
       item.hidden=!match; if(match)n++;
     }
     note.textContent=q?n+' مورد پیدا شد':'';
   };
   input.addEventListener('input',update);
 }
 const adminSearch=document.querySelector('[data-admin-search]');
 if(adminSearch){
   const rows=$$('.mw-admin-recent [data-admin-item],.mw-admin-messages [data-admin-item]');
   attachFilter(adminSearch,rows,x=>x.textContent);
 }
 const reservationList=document.querySelector('.reservations-list');
 if(reservationList){
   const rows=$$('.reservation-card',reservationList);
   if(rows.length){
      const search=document.createElement('label');search.className='reservation-list-search';
      search.innerHTML='<span>⌕</span><input type="search" placeholder="جستجوی مراسم، تاریخ شمسی یا کد رزرو" aria-label="جستجو در رزروهای من"><button type="button" aria-label="پاک‌کردن جستجو">×</button>';
      reservationList.prepend(search);
      const field=search.querySelector('input');
      search.querySelector('button').addEventListener('click',()=>{field.value='';field.dispatchEvent(new Event('input'))});
      attachFilter(field,rows,x=>x.textContent);
   }
 }
 const commerceTable=document.querySelector('.commerce-table');
 if(commerceTable){
   const rows=$$('tbody tr',commerceTable);
   if(rows.length){
     const search=document.createElement('label');search.className='commerce-list-search';
     search.innerHTML='<span>⌕</span><input type="search" placeholder="جستجوی مشتری، تاریخ شمسی یا رزرو" aria-label="جستجوی رزروها"><button type="button" aria-label="پاک‌کردن">×</button>';
     commerceTable.closest('.table-wrap')?.insertAdjacentElement('beforebegin',search);
     const field=search.querySelector('input');
     search.querySelector('button').addEventListener('click',()=>{field.value='';field.dispatchEvent(new Event('input'))});
     attachFilter(field,rows,x=>x.textContent);
   }
 }
 $$('form').forEach(form=>{
    if(form.dataset.enhanced==='off')return;
    form.addEventListener('submit',event=>{
      if(!form.checkValidity()){event.preventDefault();form.reportValidity();return}
      const btn=event.submitter;
      if(!btn||btn.disabled)return;
      // Defer until browser builds the form POST, so the clicked submit action is preserved.
      queueMicrotask(()=>{
       btn.disabled=true;btn.classList.add('form-processing');
       btn.setAttribute('aria-busy','true');
      });
    });
 });
})();
