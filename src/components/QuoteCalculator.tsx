"use client";

import { useMemo, useState } from "react";
import {
  ArrowLeft, ArrowRight, Check, ChefHat, Coffee, Headphones,
  PartyPopper, ReceiptText, Users, Info, Phone, FileText
} from "lucide-react";
import JalaliDateField from "./JalaliDateField";
import { addons, foodPackages, formatPrice, getDatePremium, venue } from "@/lib/data";
import { cn, toFaNumber } from "@/lib/utils";

const steps = ["تاریخ و مهمان","منوی غذا","نوشیدنی","موسیقی","خدمات جانبی","جمع‌بندی"];

export default function QuoteCalculator() {
  const [step,setStep] = useState(0);
  const [date,setDate] = useState("1405/7/29");
  const [guests,setGuests] = useState(250);
  const [food,setFood] = useState("buffet");
  const [selected,setSelected] = useState<string[]>(["mocktail","djlight"]);
  const [discount,setDiscount] = useState(0);
  const [saved,setSaved] = useState<"booking"|"quote"|null>(null);
  const [customer,setCustomer] = useState({name:"",phone:""});

  const currentFood = foodPackages.find(x=>x.id===food) || foodPackages[0];
  const includedIds = currentFood.includedAddonIds || [];
  const paidSelected = selected.filter(id=>!includedIds.includes(id));
  const chosenPaid = addons.filter(x=>paidSelected.includes(x.id));
  const included = addons.filter(x=>includedIds.includes(x.id));
  const premiumRule = getDatePremium(date);
  const guestIssue = guests < Math.max(venue.minGuests,currentFood.minGuests);

  const toggle = (id:string) => {
    if(includedIds.includes(id)) return;
    setSelected(prev=>prev.includes(id)?prev.filter(x=>x!==id):[...prev,id]);
  };

  const amounts = useMemo(()=>{
    const foodTotal = currentFood.pricePerPerson * guests;
    const addTotal = chosenPaid.reduce((sum,a)=>sum+(a.mode==="perPerson"?a.price*guests:a.mode==="included"?0:a.price),0);
    const base = venue.baseHallFee + foodTotal + addTotal;
    const premium = premiumRule ? base * premiumRule.percent / 100 : 0;
    const subtotal = base + premium;
    const service = subtotal * venue.servicePercent / 100;
    const tax = subtotal * venue.taxPercent / 100;
    const final = Math.max(0,subtotal + service + tax - discount);
    return {foodTotal,addTotal,premium,subtotal,service,tax,final};
  },[guests,currentFood,chosenPaid,premiumRule,discount]);

  const categories = {
    drink: addons.filter(x=>x.category==="drink" && x.active!==false),
    music: addons.filter(x=>x.category==="music" && x.active!==false),
    extra: addons.filter(x=>x.category==="extra" && x.active!==false),
  };

  const next = () => {
    if(step===0 && guestIssue) return;
    setStep(s=>Math.min(5,s+1));
  };
  const prev = () => setStep(s=>Math.max(0,s-1));
  const canSubmit = customer.name.trim().length>2 && /^0?9\d{9}$/.test(customer.phone.replace(/\D/g,""));

  return (
    <div className="grid gap-6 pb-24 lg:grid-cols-[1fr_370px] lg:pb-0">
      <section className="soft-card overflow-hidden">
        <div className="border-b border-[#eee4d5] bg-[#fffdfa] px-4 py-4 sm:px-6">
          <div className="no-scrollbar flex gap-2 overflow-x-auto">
            {steps.map((x,i)=><button key={x} onClick={()=>setStep(i)} className={cn(
              "shrink-0 rounded-full px-4 py-2 text-xs font-bold transition",
              i===step?"bg-[#172238] text-white":i<step?"bg-[#eef6ef] text-[#237044]":"border border-slate-200 bg-white text-slate-500"
            )}>
              {i<step?<Check size={13} className="ml-1 inline"/>:null}{toFaNumber(i+1)}. {x}
            </button>)}
          </div>
        </div>

        <div className="p-4 sm:p-7">
          {step===0 && <div>
            <SectionTitle title="تاریخ و تعداد مهمان" subtitle="تاریخ جلالی مراسم و تعداد مهمان‌ها را مشخص کنید."/>
            <div className="grid gap-4 md:grid-cols-2">
              <div>
                <JalaliDateField value={date} onChange={setDate}/>
                {premiumRule && <div className="mt-3 flex gap-2 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-xs leading-6 text-amber-800">
                  <Info size={17} className="mt-1 shrink-0"/>
                  <span><b>{premiumRule.title}</b> — برای این تاریخ {toFaNumber(premiumRule.percent)}٪ ضریب تقاضا به مبلغ پایه فاکتور اضافه می‌شود.</span>
                </div>}
              </div>
              <div className="rounded-2xl border border-[#e7dece] bg-white p-4">
                <div className="mb-3 flex items-center gap-2 text-sm font-bold"><Users size={18} className="text-[#b88334]"/> تعداد مهمان</div>
                <input type="range" min={venue.minGuests} max={venue.capacity} step={10} value={guests} onChange={e=>setGuests(Number(e.target.value))} className="w-full accent-[#b88334]"/>
                <div className="mt-4 flex items-end justify-between"><span className="text-xs text-slate-500">ظرفیت تالار: {toFaNumber(venue.minGuests)} تا {toFaNumber(venue.capacity)} نفر</span><b className="text-3xl">{toFaNumber(guests)} <small className="text-sm font-normal text-slate-500">نفر</small></b></div>
                {guestIssue && <div className="mt-4 rounded-xl bg-red-50 p-3 text-xs font-bold text-red-700">حداقل مهمان برای «{currentFood.title}» {toFaNumber(currentFood.minGuests)} نفر است.</div>}
              </div>
            </div>
          </div>}

          {step===1 && <div>
            <SectionTitle title="منوی پذیرایی" subtitle="یک مدل پذیرایی انتخاب کنید؛ موارد داخل پکیج دوباره محاسبه نمی‌شوند." icon={<ChefHat className="text-[#b88334]"/>}/>
            <div className="grid gap-4 md:grid-cols-3">
              {foodPackages.map(item=><button key={item.id} onClick={()=>setFood(item.id)} className={cn(
                "relative rounded-2xl border p-5 text-right transition",
                food===item.id?"border-[#b88334] bg-[#fffbf3] ring-2 ring-[#d8b77f]/20":"border-slate-200 hover:border-[#d8b77f]"
              )}>
                {item.badge && <span className="absolute left-3 top-3 rounded-full bg-[#172238] px-2.5 py-1 text-[10px] text-white">{item.badge}</span>}
                <h3 className="mt-7 font-black">{item.title}</h3>
                <p className="mt-1 text-xs text-slate-500">{item.subtitle}</p>
                <div className="my-4 font-black text-[#9f6d29]">{formatPrice(item.pricePerPerson)} <span className="text-[10px] font-normal text-slate-500">/ نفر</span></div>
                <div className="mb-3 rounded-xl bg-white/80 p-2 text-[10px] text-slate-500">حداقل {toFaNumber(item.minGuests)} مهمان</div>
                <div className="space-y-2 border-t pt-4 text-xs text-slate-600">{item.items.map(x=><div key={x} className="flex gap-2"><Check size={14} className="shrink-0 text-green-600"/>{x}</div>)}</div>
                {!!item.includedAddonIds?.length && <div className="mt-4 rounded-xl bg-green-50 p-2 text-[10px] font-bold text-green-700">{toFaNumber(item.includedAddonIds.length)} سرویس جانبی داخل این پکیج است</div>}
              </button>)}
            </div>
          </div>}

          {step===2 && <OptionsGrid title="نوشیدنی و بار" subtitle="آب و نوشابه، شربت، بار سرد یا بار گرم را انتخاب کنید." icon={<Coffee className="text-[#b88334]"/>} options={categories.drink} selected={selected} includedIds={includedIds} toggle={toggle} guests={guests}/>}
          {step===3 && <OptionsGrid title="موسیقی و سرگرمی" subtitle="DJ، سیستم صوتی، نورپردازی، کف LED یا اجرای زنده." icon={<Headphones className="text-[#b88334]"/>} options={categories.music} selected={selected} includedIds={includedIds} toggle={toggle} guests={guests}/>}
          {step===4 && <OptionsGrid title="خدمات تکمیلی" subtitle="جزئیات مراسم را مطابق سلیقه خود کامل کنید." icon={<PartyPopper className="text-[#b88334]"/>} options={categories.extra} selected={selected} includedIds={includedIds} toggle={toggle} guests={guests}/>}
          {step===5 && <div>
            <SectionTitle title="پیش‌فاکتور برآوردی مراسم" subtitle="اطلاعات تماس را وارد کنید تا مدیریت تالار تاریخ و مبلغ نهایی را تأیید کند." icon={<ReceiptText className="text-[#b88334]"/>}/>
            <div className="rounded-2xl bg-[#fbf8f2] p-5">
              <div className="grid gap-4 text-sm sm:grid-cols-2">
                <SummaryRow label="تاریخ مراسم" value={date.replaceAll("/", " / ")}/>
                <SummaryRow label="تعداد مهمان" value={toFaNumber(guests) + " نفر"}/>
                <SummaryRow label="نوع پذیرایی" value={currentFood.title}/>
                <SummaryRow label="خدمات انتخابی" value={toFaNumber(new Set([...selected,...includedIds]).size) + " مورد"}/>
              </div>
            </div>
            <div className="mt-5 grid gap-3 sm:grid-cols-2">
              <label className="text-xs text-slate-500">نام و نام خانوادگی
                <input value={customer.name} onChange={e=>setCustomer(x=>({...x,name:e.target.value}))} className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]" placeholder="مثلاً سارا احمدی"/>
              </label>
              <label className="text-xs text-slate-500">شماره موبایل
                <input value={customer.phone} onChange={e=>setCustomer(x=>({...x,phone:e.target.value}))} inputMode="tel" dir="ltr" className="mt-2 w-full rounded-xl border border-slate-200 p-3 text-left outline-none focus:border-[#b88334]" placeholder="0912xxxxxxx"/>
              </label>
            </div>
            <label className="mt-4 block text-xs text-slate-500">کد / مبلغ تخفیف تأییدشده
              <input value={discount||""} onChange={e=>setDiscount(Number(e.target.value)||0)} inputMode="numeric" className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]" placeholder="در صورت داشتن تخفیف"/>
            </label>
            {!canSubmit && <div className="mt-3 text-[10px] text-slate-400">برای ثبت درخواست، نام و شماره موبایل معتبر وارد کنید.</div>}
            {saved && <div className="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-700">
              {saved==="quote"?"درخواست پیش‌فاکتور ثبت شد.":"درخواست رزرو و بازدید ثبت شد."} کارشناسان تالار برای تأیید تاریخ و مبلغ نهایی تماس می‌گیرند.
            </div>}
          </div>}

          <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[#eee4d5] pt-5">
            <button disabled={step===0} onClick={prev} className="btn-outline disabled:cursor-not-allowed disabled:opacity-40"><ArrowRight size={17}/> مرحله قبل</button>
            {step<5
              ? <button disabled={step===0&&guestIssue} onClick={next} className="btn-gold disabled:cursor-not-allowed disabled:opacity-50">مرحله بعد <ArrowLeft size={17}/></button>
              : <div className="flex flex-wrap gap-2">
                  <button disabled={!canSubmit} onClick={()=>setSaved("quote")} className="btn-outline disabled:opacity-40"><FileText size={17}/> ارسال پیش‌فاکتور</button>
                  <button disabled={!canSubmit} onClick={()=>setSaved("booking")} className="btn-gold disabled:opacity-40"><Phone size={17}/> درخواست رزرو و بازدید</button>
                </div>}
          </div>
        </div>
      </section>

      <aside className="h-fit lg:sticky lg:top-24">
        <CostSummary guests={guests} currentFood={currentFood} chosenPaid={chosenPaid} included={included} premiumRule={premiumRule} amounts={amounts} discount={discount}/>
      </aside>

      <div className="fixed inset-x-0 bottom-0 z-40 border-t border-[#eadfce] bg-white/95 p-3 shadow-[0_-10px_30px_rgba(23,34,56,.08)] backdrop-blur lg:hidden">
        <div className="mx-auto flex max-w-xl items-center justify-between gap-3">
          <div><div className="text-[10px] text-slate-500">جمع برآوردی</div><div className="text-base font-black text-[#9f6d29]">{formatPrice(amounts.final)}</div></div>
          <button onClick={()=>setStep(5)} className="btn-gold px-4 py-2.5 text-xs">مشاهده پیش‌فاکتور <ArrowLeft size={15}/></button>
        </div>
      </div>
    </div>
  );
}

function CostSummary({guests,currentFood,chosenPaid,included,premiumRule,amounts,discount}:{
  guests:number; currentFood:(typeof foodPackages)[number]; chosenPaid:typeof addons; included:typeof addons;
  premiumRule:ReturnType<typeof getDatePremium>; amounts:{foodTotal:number;addTotal:number;premium:number;subtotal:number;service:number;tax:number;final:number}; discount:number;
}) {
  return <div className="soft-card overflow-hidden">
    <div className="bg-[#172238] px-5 py-4 text-white"><h3 className="font-black">خلاصه هزینه مراسم</h3><p className="mt-1 text-xs text-white/60">برآورد اولیه — قابل تأیید توسط تالار</p></div>
    <div className="space-y-3 p-5 text-sm">
      <SummaryRow label="اجاره پایه سالن" value={formatPrice(venue.baseHallFee)}/>
      <SummaryRow label={toFaNumber(guests) + " نفر × " + currentFood.title} value={formatPrice(amounts.foodTotal)}/>
      {included.map(x=><SummaryRow key={x.id} label={x.title+" (داخل پکیج)"} value="رایگان" accent/>)}
      {chosenPaid.map(x=><SummaryRow key={x.id} label={x.title} value={formatPrice(x.mode==="perPerson"?x.price*guests:x.price)}/>)}
      {premiumRule && <SummaryRow label={premiumRule.title+" ("+toFaNumber(premiumRule.percent)+"٪)"} value={formatPrice(amounts.premium)}/>}
      <div className="gold-line my-4"/>
      <SummaryRow label={"حق سرویس " + toFaNumber(venue.servicePercent) + "٪"} value={formatPrice(amounts.service)}/>
      {venue.taxPercent>0 && <SummaryRow label={"مالیات " + toFaNumber(venue.taxPercent) + "٪"} value={formatPrice(amounts.tax)}/>}
      {discount>0 && <SummaryRow label="تخفیف" value={"− " + formatPrice(discount)} accent/>}
      <div className="mt-4 rounded-2xl bg-[#fbf8f2] p-4"><div className="text-xs text-slate-500">جمع برآوردی</div><div className="mt-1 text-2xl font-black text-[#9f6d29]">{formatPrice(amounts.final)}</div></div>
      <p className="pt-2 text-[10px] leading-6 text-slate-500">* این مبلغ تخمینی است و پس از بررسی تاریخ، تعداد مهمان و جزئیات خدمات توسط مدیریت تالار تأیید می‌شود.</p>
    </div>
  </div>
}

function OptionsGrid({title,subtitle,icon,options,selected,includedIds,toggle,guests}:{
  title:string;subtitle:string;icon:React.ReactNode;options:typeof addons;selected:string[];includedIds:string[];toggle:(id:string)=>void;guests:number
}) {
  return <div>
    <SectionTitle title={title} subtitle={subtitle} icon={icon}/>
    <div className="grid gap-3 sm:grid-cols-2">
      {options.map(item=>{
        const isIncluded=includedIds.includes(item.id);
        const active=isIncluded||selected.includes(item.id);
        const price=item.mode==="perPerson"?item.price*guests:item.price;
        return <button key={item.id} disabled={isIncluded} onClick={()=>toggle(item.id)} className={cn(
          "flex items-start gap-4 rounded-2xl border p-4 text-right transition",
          active?"border-[#b88334] bg-[#fffbf3]":"border-slate-200 hover:border-[#d8b77f]",
          isIncluded&&"cursor-default border-green-200 bg-green-50"
        )}>
          <span className={cn("mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border",active?"border-[#b88334] bg-[#b88334] text-white":"border-slate-300")}>{active?<Check size={14}/>:null}</span>
          <span className="flex-1">
            <span className="flex flex-wrap items-center gap-2"><b>{item.title}</b>{isIncluded&&<em className="rounded-full bg-green-100 px-2 py-1 text-[9px] not-italic font-bold text-green-700">داخل پکیج</em>}</span>
            <span className="mt-1 block text-xs leading-6 text-slate-500">{item.description}</span>
            <span className="mt-3 block text-sm font-black text-[#9f6d29]">{isIncluded?"بدون هزینه اضافه":formatPrice(price)}{!isIncluded&&item.mode==="perPerson"?<small className="mr-1 font-normal text-slate-400">(برای {toFaNumber(guests)} نفر)</small>:null}</span>
          </span>
        </button>
      })}
    </div>
  </div>
}

function SectionTitle({title,subtitle,icon}:{title:string;subtitle:string;icon?:React.ReactNode}) {
  return <div className="mb-6"><h2 className="flex items-center gap-2 text-xl font-black">{icon}{title}</h2><p className="mt-2 text-sm text-slate-500">{subtitle}</p></div>
}

function SummaryRow({label,value,accent=false}:{label:string;value:string;accent?:boolean}) {
  return <div className="flex items-start justify-between gap-4"><span className="text-slate-500">{label}</span><b className={accent?"text-green-700":"text-[#172238]"}>{value}</b></div>
}
