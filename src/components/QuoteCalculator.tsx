"use client";

import { useMemo, useState } from "react";
import { ArrowLeft, ArrowRight, Check, ChefHat, Coffee, Headphones, PartyPopper, ReceiptText, Users } from "lucide-react";
import JalaliDateField from "./JalaliDateField";
import { addons, foodPackages, formatPrice, venue } from "@/lib/data";
import { cn, toFaNumber } from "@/lib/utils";

const steps = ["تاریخ و مهمان","منوی غذا","نوشیدنی","موسیقی","خدمات جانبی","جمع‌بندی"];

export default function QuoteCalculator() {
  const [step,setStep] = useState(0);
  const [date,setDate] = useState("1405/7/29");
  const [guests,setGuests] = useState(250);
  const [food,setFood] = useState("buffet");
  const [selected,setSelected] = useState<string[]>(["soft","dj"]);
  const [discount,setDiscount] = useState(0);
  const [saved,setSaved] = useState(false);

  const currentFood = foodPackages.find(x=>x.id===food) || foodPackages[0];
  const chosen = addons.filter(x=>selected.includes(x.id));
  const toggle = (id:string) => setSelected(prev=>prev.includes(id)?prev.filter(x=>x!==id):[...prev,id]);

  const amounts = useMemo(()=>{
    const foodTotal = currentFood.pricePerPerson * guests;
    const addTotal = chosen.reduce((sum,a)=>sum+(a.mode==="perPerson"?a.price*guests:a.price),0);
    const subtotal = venue.baseHallFee + foodTotal + addTotal;
    const service = subtotal * venue.servicePercent / 100;
    const tax = subtotal * venue.taxPercent / 100;
    const final = subtotal + service + tax - discount;
    return {foodTotal,addTotal,subtotal,service,tax,final};
  },[guests,currentFood,chosen,discount]);

  const categories = {
    drink: addons.filter(x=>x.category==="drink"),
    music: addons.filter(x=>x.category==="music"),
    extra: addons.filter(x=>x.category==="extra"),
  };

  const next = () => setStep(s=>Math.min(5,s+1));
  const prev = () => setStep(s=>Math.max(0,s-1));

  return (
    <div className="grid gap-6 lg:grid-cols-[1fr_360px]">
      <section className="soft-card overflow-hidden">
        <div className="border-b border-[#eee4d5] bg-[#fffdfa] px-4 py-4 sm:px-6">
          <div className="no-scrollbar flex gap-2 overflow-x-auto">
            {steps.map((x,i)=><button key={x} onClick={()=>setStep(i)} className={cn("shrink-0 rounded-full px-4 py-2 text-xs font-bold transition", i===step?"bg-[#172238] text-white":i<step?"bg-[#eef6ef] text-[#237044]":"bg-white text-slate-500 border border-slate-200")}>
              {i<step?<Check size={13} className="ml-1 inline"/>:null}{toFaNumber(i+1)}. {x}
            </button>)}
          </div>
        </div>

        <div className="p-4 sm:p-7">
          {step===0 && <div>
            <div className="mb-6"><h2 className="text-xl font-black">تاریخ و تعداد مهمان</h2><p className="mt-2 text-sm text-slate-500">تاریخ مراسم و تعداد مهمان‌ها را مشخص کنید.</p></div>
            <div className="grid gap-4 md:grid-cols-2">
              <JalaliDateField value={date} onChange={setDate}/>
              <div className="rounded-2xl border border-[#e7dece] bg-white p-4">
                <div className="mb-3 flex items-center gap-2 text-sm font-bold"><Users size={18} className="text-[#b88334]"/> تعداد مهمان</div>
                <input type="range" min={venue.minGuests} max={venue.capacity} step={10} value={guests} onChange={e=>setGuests(Number(e.target.value))} className="w-full accent-[#b88334]"/>
                <div className="mt-4 flex items-end justify-between"><span className="text-xs text-slate-500">بین {toFaNumber(venue.minGuests)} تا {toFaNumber(venue.capacity)} نفر</span><b className="text-3xl">{toFaNumber(guests)} <small className="text-sm font-normal text-slate-500">نفر</small></b></div>
              </div>
            </div>
          </div>}

          {step===1 && <div>
            <div className="mb-6"><h2 className="flex items-center gap-2 text-xl font-black"><ChefHat className="text-[#b88334]"/> منوی پذیرایی</h2><p className="mt-2 text-sm text-slate-500">یکی از سبک‌های پذیرایی را انتخاب کنید.</p></div>
            <div className="grid gap-4 md:grid-cols-3">
              {foodPackages.map(item=><button key={item.id} onClick={()=>setFood(item.id)} className={cn("relative rounded-2xl border p-5 text-right transition",food===item.id?"border-[#b88334] bg-[#fffbf3] ring-2 ring-[#d8b77f]/20":"border-slate-200 hover:border-[#d8b77f]")}>
                {item.badge && <span className="absolute left-3 top-3 rounded-full bg-[#172238] px-2.5 py-1 text-[10px] text-white">{item.badge}</span>}
                <h3 className="mt-7 font-black">{item.title}</h3><p className="mt-1 text-xs text-slate-500">{item.subtitle}</p>
                <div className="my-4 font-black text-[#9f6d29]">{formatPrice(item.pricePerPerson)} <span className="text-[10px] font-normal text-slate-500">/ نفر</span></div>
                <div className="space-y-2 border-t pt-4 text-xs text-slate-600">{item.items.map(x=><div key={x} className="flex gap-2"><Check size={14} className="shrink-0 text-green-600"/>{x}</div>)}</div>
              </button>)}
            </div>
          </div>}

          {step===2 && <OptionsGrid title="نوشیدنی و بار" subtitle="پکیج‌های نوشیدنی دلخواه را به مراسم اضافه کنید." icon={<Coffee className="text-[#b88334]"/>} options={categories.drink} selected={selected} toggle={toggle} guests={guests}/>}
          {step===3 && <OptionsGrid title="موسیقی و سرگرمی" subtitle="DJ، نورپردازی یا اجرای زنده را انتخاب کنید." icon={<Headphones className="text-[#b88334]"/>} options={categories.music} selected={selected} toggle={toggle} guests={guests}/>}
          {step===4 && <OptionsGrid title="خدمات تکمیلی" subtitle="جزئیات مراسم را مطابق سلیقه خود کامل کنید." icon={<PartyPopper className="text-[#b88334]"/>} options={categories.extra} selected={selected} toggle={toggle} guests={guests}/>}
          {step===5 && <div>
            <div className="mb-6"><h2 className="flex items-center gap-2 text-xl font-black"><ReceiptText className="text-[#b88334]"/> پیش‌فاکتور برآوردی مراسم</h2><p className="mt-2 text-sm text-slate-500">جزئیات انتخابی شما قبل از تأیید نهایی تالار.</p></div>
            <div className="rounded-2xl bg-[#fbf8f2] p-5">
              <div className="grid gap-4 text-sm sm:grid-cols-2">
                <SummaryRow label="تاریخ مراسم" value={date.replaceAll("/", " / ")}/>
                <SummaryRow label="تعداد مهمان" value={toFaNumber(guests) + " نفر"}/>
                <SummaryRow label="نوع پذیرایی" value={currentFood.title}/>
                <SummaryRow label="خدمات انتخابی" value={toFaNumber(chosen.length) + " مورد"}/>
              </div>
            </div>
            <label className="mt-5 block text-xs text-slate-500">مبلغ تخفیف دستی (برای پیش‌نمایش)
              <input value={discount||""} onChange={e=>setDiscount(Number(e.target.value)||0)} inputMode="numeric" className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]" placeholder="مثلاً 10000000"/>
            </label>
            {saved && <div className="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-700">درخواست شما با موفقیت ثبت شد. کارشناسان تالار برای تأیید تاریخ و مبلغ نهایی تماس می‌گیرند.</div>}
          </div>}

          <div className="mt-8 flex items-center justify-between border-t border-[#eee4d5] pt-5">
            <button disabled={step===0} onClick={prev} className="btn-outline disabled:cursor-not-allowed disabled:opacity-40"><ArrowRight size={17}/> مرحله قبل</button>
            {step<5 ? <button onClick={next} className="btn-gold">مرحله بعد <ArrowLeft size={17}/></button> : <button onClick={()=>setSaved(true)} className="btn-gold">درخواست رزرو و بازدید <Check size={17}/></button>}
          </div>
        </div>
      </section>

      <aside className="h-fit lg:sticky lg:top-24">
        <div className="soft-card overflow-hidden">
          <div className="bg-[#172238] px-5 py-4 text-white"><h3 className="font-black">خلاصه هزینه مراسم</h3><p className="mt-1 text-xs text-white/60">برآورد اولیه — قابل تأیید توسط تالار</p></div>
          <div className="space-y-3 p-5 text-sm">
            <SummaryRow label="اجاره پایه سالن" value={formatPrice(venue.baseHallFee)}/>
            <SummaryRow label={toFaNumber(guests) + " نفر × " + currentFood.title} value={formatPrice(amounts.foodTotal)}/>
            {chosen.map(x=><SummaryRow key={x.id} label={x.title} value={formatPrice(x.mode==="perPerson"?x.price*guests:x.price)}/>)}
            <div className="gold-line my-4"/>
            <SummaryRow label={"حق سرویس " + toFaNumber(venue.servicePercent) + "٪"} value={formatPrice(amounts.service)}/>
            {discount>0 && <SummaryRow label="تخفیف" value={"− " + formatPrice(discount)} accent/>}
            <div className="mt-4 rounded-2xl bg-[#fbf8f2] p-4">
              <div className="text-xs text-slate-500">جمع برآوردی</div><div className="mt-1 text-2xl font-black text-[#9f6d29]">{formatPrice(amounts.final)}</div>
            </div>
            <p className="pt-2 text-[10px] leading-6 text-slate-500">* مبلغ نهایی پس از بررسی تاریخ، تعداد مهمان و جزئیات خدمات توسط مدیریت تالار تأیید می‌شود.</p>
          </div>
        </div>
      </aside>
    </div>
  );
}

function OptionsGrid({title,subtitle,icon,options,selected,toggle,guests}:{title:string;subtitle:string;icon:React.ReactNode;options:typeof addons;selected:string[];toggle:(id:string)=>void;guests:number}) {
  return <div>
    <div className="mb-6"><h2 className="flex items-center gap-2 text-xl font-black">{icon}{title}</h2><p className="mt-2 text-sm text-slate-500">{subtitle}</p></div>
    <div className="grid gap-3 sm:grid-cols-2">
      {options.map(item=>{
        const active=selected.includes(item.id);
        const price=item.mode==="perPerson"?item.price*guests:item.price;
        return <button key={item.id} onClick={()=>toggle(item.id)} className={cn("flex items-start gap-4 rounded-2xl border p-4 text-right transition",active?"border-[#b88334] bg-[#fffbf3]":"border-slate-200 hover:border-[#d8b77f]")}>
          <span className={cn("mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border",active?"border-[#b88334] bg-[#b88334] text-white":"border-slate-300")}>{active?<Check size={14}/>:null}</span>
          <span className="flex-1"><b className="block">{item.title}</b><span className="mt-1 block text-xs leading-6 text-slate-500">{item.description}</span><span className="mt-3 block text-sm font-black text-[#9f6d29]">{formatPrice(price)}{item.mode==="perPerson"?<small className="mr-1 font-normal text-slate-400">(برای {toFaNumber(guests)} نفر)</small>:null}</span></span>
        </button>
      })}
    </div>
  </div>
}

function SummaryRow({label,value,accent=false}:{label:string;value:string;accent?:boolean}) {
  return <div className="flex items-start justify-between gap-4"><span className="text-slate-500">{label}</span><b className={accent?"text-green-700":"text-[#172238]"}>{value}</b></div>
}
