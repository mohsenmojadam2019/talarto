"use client";

import { useMemo, useState } from "react";
import { addons, foodPackages, formatPrice, recentQuotes } from "@/lib/data";
import { toFaNumber } from "@/lib/utils";
import { Check, ChevronLeft, Edit3, ImageIcon, Plus, Search, Trash2, X } from "lucide-react";
import { Status } from "./AdminDashboard";

type Row = { id:string; title:string; subtitle?:string; price?:number; date?:string; guests?:number; status?:string; mode?:string; active?:boolean };

const names: Record<string,{title:string;desc:string;add:string}> = {
  quotes:{title:"استعلام‌ها و پیش‌فاکتورها",desc:"پیگیری درخواست‌های مشتری و وضعیت تبدیل به رزرو",add:"ثبت پیش‌فاکتور"},
  bookings:{title:"رزروها",desc:"مدیریت رزروهای قطعی، پیش‌پرداخت و قراردادها",add:"ثبت رزرو"},
  calendar:{title:"تقویم مراسم",desc:"نمایش تاریخ‌های آزاد، رزرو و مناسبت‌ها بر اساس تقویم جلالی",add:"ثبت تاریخ ویژه"},
  food:{title:"منو و غذا",desc:"مدیریت دیس‌پرس، سلف، سلف VIP و قیمت نفری",add:"افزودن منو"},
  drinks:{title:"نوشیدنی‌ها",desc:"مدیریت بار سرد، نوشیدنی، چای و قهوه",add:"افزودن نوشیدنی"},
  music:{title:"موسیقی و DJ",desc:"تعریف دیجی، سیستم صوتی، نورپردازی و اجرای زنده",add:"افزودن سرویس موسیقی"},
  extras:{title:"خدمات جانبی",desc:"گل‌آرایی، عکاسی، مه سرد و سایر آپشن‌های مراسم",add:"افزودن خدمت"},
  gallery:{title:"گالری تصاویر",desc:"مدیریت تصاویر سایت، سالن و مراسم",add:"بارگذاری تصویر"},
  messages:{title:"پیام‌ها",desc:"درخواست‌های تماس و پیام‌های مشتریان",add:"پیام جدید"},
  settings:{title:"تنظیمات تالار",desc:"هزینه پایه، ظرفیت، حق سرویس، اطلاعات تماس و تنظیمات قیمت",add:"ذخیره تنظیمات"},
};

function seed(section:string): Row[] {
  if(section==="food") return foodPackages.map(x=>({id:x.id,title:x.title,subtitle:x.subtitle,price:x.pricePerPerson,mode:"نفری",active:true}));
  if(section==="drinks") return addons.filter(x=>x.category==="drink").map(x=>({id:x.id,title:x.title,subtitle:x.description,price:x.price,mode:x.mode==="perPerson"?"نفری":"ثابت",active:true}));
  if(section==="music") return addons.filter(x=>x.category==="music").map(x=>({id:x.id,title:x.title,subtitle:x.description,price:x.price,mode:"ثابت",active:true}));
  if(section==="extras") return addons.filter(x=>x.category==="extra").map(x=>({id:x.id,title:x.title,subtitle:x.description,price:x.price,mode:x.mode==="perPerson"?"نفری":"ثابت",active:true}));
  if(section==="quotes"||section==="bookings") return recentQuotes.filter(x=>section==="quotes"||x.status==="رزرو قطعی").map(x=>({id:x.id,title:x.name,subtitle:x.type,price:x.total,date:x.date,guests:x.guests,status:x.status}));
  if(section==="messages") return [
    {id:"m1",title:"مریم رضایی",subtitle:"درباره ظرفیت سالن اصلی و قیمت منوی سلف سؤال داشتم.",date:"۱۴۰۵/۰۶/۲۹ - ۱۴:۲۰",status:"جدید"},
    {id:"m2",title:"علیرضا نادری",subtitle:"آیا امکان بازدید حضوری پنجشنبه وجود دارد؟",date:"۱۴۰۵/۰۶/۲۹ - ۱۲:۴۵",status:"پاسخ داده شد"},
    {id:"m3",title:"الهه کریمی",subtitle:"هزینه شام برای ۱۵۰ نفر را می‌فرمایید؟",date:"۱۴۰۵/۰۶/۲۹ - ۱۰:۳۰",status:"جدید"},
  ];
  return [];
}

export default function AdminSection({section}:{section:string}) {
  const meta = names[section] || names.settings;
  const [rows,setRows]=useState<Row[]>(()=>seed(section));
  const [q,setQ]=useState("");
  const [modal,setModal]=useState(false);
  const [edit,setEdit]=useState<Row|null>(null);
  const [toast,setToast]=useState("");
  const filtered=useMemo(()=>rows.filter(x=>(x.title+" "+(x.subtitle||"")).includes(q)),[rows,q]);

  if(section==="calendar") return <CalendarAdmin meta={meta}/>;
  if(section==="gallery") return <GalleryAdmin meta={meta}/>;
  if(section==="settings") return <SettingsAdmin meta={meta}/>;

  const save=(form:FormData)=>{
    const title=String(form.get("title")||"").trim();
    if(!title)return;
    const row:Row={id:edit?.id||("id"+Date.now()),title,subtitle:String(form.get("subtitle")||""),price:Number(form.get("price")||0),mode:String(form.get("mode")||"ثابت"),active:true,date:edit?.date||"۱۴۰۵/۰۷/۱۵",status:edit?.status||"جدید"};
    setRows(prev=>edit?prev.map(x=>x.id===edit.id?{...x,...row}:x):[row,...prev]);
    setModal(false);setEdit(null);setToast("تغییرات با موفقیت ذخیره شد");setTimeout(()=>setToast(""),2400);
  };
  const remove=(id:string)=>{if(confirm("این مورد حذف شود؟"))setRows(x=>x.filter(r=>r.id!==id))};

  return <div className="mx-auto max-w-[1500px]">
    <div className="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><h1 className="text-xl font-black sm:text-2xl">{meta.title}</h1><p className="mt-1 text-xs text-slate-500">{meta.desc}</p></div><button onClick={()=>{setEdit(null);setModal(true)}} className="btn-gold"><Plus size={17}/>{meta.add}</button></div>
    {toast&&<div className="fixed left-5 top-24 z-50 flex items-center gap-2 rounded-2xl bg-[#172238] px-4 py-3 text-xs font-bold text-white shadow-2xl"><Check size={16} className="text-green-400"/>{toast}</div>}
    <section className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div className="flex flex-col gap-3 border-b p-4 sm:flex-row sm:items-center">
        <label className="relative flex-1"><Search size={17} className="absolute right-3 top-3.5 text-slate-400"/><input value={q} onChange={e=>setQ(e.target.value)} placeholder="جستجو..." className="w-full rounded-xl border border-slate-200 py-3 pl-3 pr-10 text-xs outline-none focus:border-[#b88334]"/></label>
        <select className="rounded-xl border border-slate-200 px-3 py-3 text-xs"><option>همه وضعیت‌ها</option><option>فعال</option><option>رزرو قطعی</option><option>جدید</option></select>
        <div className="text-[10px] text-slate-400">{toFaNumber(filtered.length)} مورد</div>
      </div>
      {filtered.length===0?<div className="py-20 text-center text-sm text-slate-400">موردی پیدا نشد.</div>:<>
      <div className="hidden overflow-x-auto md:block"><table className="w-full text-right text-xs"><thead className="bg-slate-50 text-slate-500"><tr><th className="px-4 py-3">عنوان / مشتری</th><th className="px-4 py-3">جزئیات</th><th className="px-4 py-3">تاریخ / نوع قیمت</th><th className="px-4 py-3">مبلغ</th><th className="px-4 py-3">وضعیت</th><th className="px-4 py-3">عملیات</th></tr></thead><tbody>
        {filtered.map(row=><tr key={row.id} className="border-t border-slate-100 hover:bg-[#fffdfa]"><td className="px-4 py-4 font-bold">{row.title}</td><td className="max-w-xs px-4 py-4 text-slate-500">{row.subtitle}{row.guests?" • "+toFaNumber(row.guests)+" نفر":""}</td><td className="px-4 py-4">{row.date||row.mode||"—"}</td><td className="px-4 py-4 font-bold text-[#9f6d29]">{row.price?formatPrice(row.price):"—"}</td><td className="px-4 py-4">{row.status?<Status value={row.status}/>:<span className="rounded-full bg-green-50 px-2 py-1 text-[9px] font-bold text-green-700">فعال</span>}</td><td className="px-4 py-4"><div className="flex gap-2"><button onClick={()=>{setEdit(row);setModal(true)}} className="rounded-lg border p-2 text-slate-500 hover:text-[#b88334]"><Edit3 size={15}/></button><button onClick={()=>remove(row.id)} className="rounded-lg border p-2 text-slate-500 hover:text-red-600"><Trash2 size={15}/></button></div></td></tr>)}
      </tbody></table></div>
      <div className="divide-y md:hidden">{filtered.map(row=><div key={row.id} className="p-4"><div className="flex items-start justify-between gap-3"><div><b className="block text-sm">{row.title}</b><p className="mt-1 text-[10px] leading-5 text-slate-500">{row.subtitle}</p></div>{row.status?<Status value={row.status}/>:<span className="rounded-full bg-green-50 px-2 py-1 text-[9px] font-bold text-green-700">فعال</span>}</div><div className="mt-3 flex items-center justify-between"><span className="text-xs font-bold text-[#9f6d29]">{row.price?formatPrice(row.price):row.date}</span><div className="flex gap-2"><button onClick={()=>{setEdit(row);setModal(true)}} className="rounded-lg border p-2"><Edit3 size={14}/></button><button onClick={()=>remove(row.id)} className="rounded-lg border p-2"><Trash2 size={14}/></button></div></div></div>)}</div></>}
    </section>

    {modal&&<div className="fixed inset-0 z-50 grid place-items-center bg-[#172238]/35 p-4"><form action={save} className="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl"><div className="mb-5 flex items-center justify-between"><h2 className="font-black">{edit?"ویرایش":"افزودن"} {meta.title}</h2><button type="button" onClick={()=>setModal(false)} className="rounded-xl border p-2"><X size={17}/></button></div>
      <div className="space-y-4"><label className="block text-xs text-slate-500">عنوان<input name="title" required defaultValue={edit?.title} className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label><label className="block text-xs text-slate-500">توضیحات<textarea name="subtitle" defaultValue={edit?.subtitle} rows={3} className="mt-2 w-full resize-none rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label><div className="grid grid-cols-2 gap-3"><label className="text-xs text-slate-500">قیمت<input name="price" type="number" defaultValue={edit?.price} className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label><label className="text-xs text-slate-500">نوع قیمت<select name="mode" defaultValue={edit?.mode||"ثابت"} className="mt-2 w-full rounded-xl border border-slate-200 bg-white p-3"><option>ثابت</option><option>نفری</option><option>داخل پکیج</option></select></label></div></div>
      <div className="mt-6 flex justify-end gap-2"><button type="button" className="btn-outline" onClick={()=>setModal(false)}>انصراف</button><button className="btn-gold">ذخیره تغییرات</button></div>
    </form></div>}
  </div>
}

function CalendarAdmin({meta}:{meta:{title:string;desc:string;add:string}}) {
  const booked=[3,7,10,15,22,29];
  return <div className="mx-auto max-w-[1500px]"><div className="mb-5 flex items-end justify-between"><div><h1 className="text-2xl font-black">{meta.title}</h1><p className="mt-1 text-xs text-slate-500">{meta.desc}</p></div><button className="btn-gold"><Plus size={17}/>{meta.add}</button></div>
    <div className="grid gap-4 xl:grid-cols-[1fr_340px]"><section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><div className="mb-6 flex items-center justify-between"><button className="rounded-xl border p-2"><ChevronLeft size={18}/></button><div className="text-center"><b>مهر ۱۴۰۵</b><div className="text-[10px] text-slate-400">تقویم جلالی</div></div><button className="rotate-180 rounded-xl border p-2"><ChevronLeft size={18}/></button></div><div className="grid grid-cols-7 gap-2 text-center text-xs text-slate-400">{["شنبه","یک","دو","سه","چهار","پنج","جمعه"].map(x=><div key={x} className="py-2">{x}</div>)}{Array.from({length:35},(_,i)=>i-1).map((d,i)=>d<1||d>31?<div key={i}/>:<button key={i} className={"min-h-20 rounded-2xl border p-2 text-right align-top transition "+(booked.includes(d)?"border-[#d8b77f] bg-[#fff9ef]":"border-slate-100 hover:border-[#d8b77f]")}><span className="font-bold text-[#172238]">{toFaNumber(d)}</span>{booked.includes(d)&&<div className="mt-4 rounded-full bg-[#b88334] px-2 py-1 text-center text-[9px] text-white">رزرو</div>}</button>)}</div></section>
    <aside className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><h2 className="font-black">وضعیت ماه</h2><div className="mt-5 space-y-3">{[["روزهای رزرو","۶","bg-[#b88334]"],["روزهای آزاد","۲۲","bg-green-500"],["تعطیلات / ویژه","۳","bg-red-400"]].map(([t,v,c])=><div key={t} className="flex items-center gap-3 rounded-2xl bg-slate-50 p-4"><span className={"h-3 w-3 rounded-full "+c}/><span className="flex-1 text-xs">{t}</span><b>{toFaNumber(v)}</b></div>)}</div><div className="mt-5 rounded-2xl bg-[#fbf8f2] p-4 text-xs leading-7 text-slate-600">قیمت روزهای پنجشنبه، جمعه و مناسبت‌های خاص را می‌توانید از تنظیمات قیمت‌گذاری مستقل تعیین کنید.</div></aside></div>
  </div>
}

function GalleryAdmin({meta}:{meta:{title:string;desc:string;add:string}}) {
  const [count,setCount]=useState(8);
  return <div className="mx-auto max-w-[1500px]"><div className="mb-5 flex items-end justify-between"><div><h1 className="text-2xl font-black">{meta.title}</h1><p className="mt-1 text-xs text-slate-500">{meta.desc}</p></div><button onClick={()=>setCount(x=>x+1)} className="btn-gold"><Plus size={17}/>{meta.add}</button></div><div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">{Array.from({length:count},(_,i)=><div key={i} className="group relative aspect-[4/3] overflow-hidden rounded-2xl bg-gradient-to-br from-[#fbf3e7] to-[#e9dcc7]"><ImageIcon className="absolute inset-0 m-auto text-[#b88334]" size={32}/><div className="absolute inset-x-2 bottom-2 flex justify-between rounded-xl bg-white/90 p-2 opacity-0 transition group-hover:opacity-100"><span className="text-[10px]">تصویر {toFaNumber(i+1)}</span><button onClick={()=>setCount(x=>Math.max(0,x-1))}><Trash2 size={14} className="text-red-500"/></button></div></div>)}</div></div>
}

function SettingsAdmin({meta}:{meta:{title:string;desc:string;add:string}}) {
  const [saved,setSaved]=useState(false);
  return <div className="mx-auto max-w-[1000px]"><div className="mb-5"><h1 className="text-2xl font-black">{meta.title}</h1><p className="mt-1 text-xs text-slate-500">{meta.desc}</p></div><form onSubmit={e=>{e.preventDefault();setSaved(true);setTimeout(()=>setSaved(false),2400)}} className="space-y-4">
    <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><h2 className="font-black">قیمت و ظرفیت</h2><div className="mt-5 grid gap-4 sm:grid-cols-2"><Field label="هزینه پایه سالن (تومان)" value="85000000"/><Field label="حداقل مهمان" value="100"/><Field label="حداکثر ظرفیت" value="650"/><Field label="حق سرویس (%)" value="10"/><Field label="مالیات (%)" value="0"/><Field label="ضریب پنجشنبه/جمعه (%)" value="15"/></div></section>
    <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><h2 className="font-black">اطلاعات تالار</h2><div className="mt-5 grid gap-4 sm:grid-cols-2"><Field label="نام برند" value="talarto"/><Field label="شماره تماس" value="02191012345"/><Field label="نام تالار" value="تالار رویای ماندگار"/><Field label="اینستاگرام" value="@talarto"/></div><label className="mt-4 block text-xs text-slate-500">آدرس<textarea defaultValue="تهران، مجموعه تالار رویای ماندگار" rows={3} className="mt-2 w-full resize-none rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label></section>
    <div className="flex items-center justify-end gap-3">{saved&&<span className="text-xs font-bold text-green-600">✓ تنظیمات ذخیره شد</span>}<button className="btn-gold">ذخیره تنظیمات</button></div>
  </form></div>
}
function Field({label,value}:{label:string;value:string}){return <label className="text-xs text-slate-500">{label}<input defaultValue={value} className="mt-2 w-full rounded-xl border border-slate-200 p-3 text-sm outline-none focus:border-[#b88334]"/></label>}
