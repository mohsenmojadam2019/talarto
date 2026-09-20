import { CalendarDays, CheckCircle2, CircleDollarSign, MessageCircle, Star, Users } from "lucide-react";
import Link from "next/link";
import { formatPrice, recentQuotes } from "@/lib/data";
import { toFaNumber } from "@/lib/utils";

const months = [["فروردین",8],["اردیبهشت",12],["خرداد",10],["تیر",16],["مرداد",23],["شهریور",28],["مهر",31],["آبان",23],["آذر",19],["دی",33],["بهمن",27],["اسفند",21]];

export default function AdminDashboard() {
  return (
    <div className="mx-auto max-w-[1500px]">
      <div className="mb-5 flex items-end justify-between gap-4">
        <div><h1 className="text-xl font-black sm:text-2xl">داشبورد مدیریت</h1><p className="mt-1 text-xs text-slate-500">نمای کلی عملکرد تالار در سال ۱۴۰۵</p></div>
        <button className="rounded-xl bg-[#172238] px-4 py-2.5 text-xs font-bold text-white">+ ثبت رزرو جدید</button>
      </div>

      <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <Kpi icon={MessageCircle} label="استعلام‌های امروز" value="۱۲" change="+۳۳٪"/>
        <Kpi icon={CheckCircle2} label="رزروهای قطعی" value="۲۳" change="+۲۷٪"/>
        <Kpi icon={CalendarDays} label="مراسم پیش رو" value="۸" hint="در ۳۰ روز آینده"/>
        <Kpi icon={CircleDollarSign} label="درآمد این ماه" value="۱۲۸,۵۰۰,۰۰۰" suffix="تومان" change="+۱۲٪"/>
        <Kpi icon={Star} label="رضایت مشتریان" value="۹۴٪" hint="از ۲۴۸ نظر"/>
      </div>

      <div className="mt-4 grid gap-4 xl:grid-cols-[1.1fr_.85fr_.8fr]">
        <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <div className="flex items-center justify-between"><div><h2 className="font-black">نمودار رزروهای ماهانه</h2><p className="mt-1 text-[10px] text-slate-500">تعداد رزروهای ثبت‌شده در ۱۴۰۵</p></div><select className="rounded-xl border border-slate-200 px-3 py-2 text-xs"><option>همه مراسم</option><option>عروسی</option><option>عقد</option></select></div>
          <div className="mt-8 flex h-64 items-end gap-2 border-b border-slate-100 pb-2">
            {months.map(([m,v],i)=><div key={String(m)} className="flex h-full flex-1 flex-col items-center justify-end gap-2"><div className={"w-full max-w-8 rounded-t-lg "+(i===5?"bg-[#b88334]":"bg-[#e7d2af]")} style={{height:(Number(v)*5.5)+"px"}} title={String(v)}/><span className="rotate-[-40deg] text-[9px] text-slate-500 sm:rotate-0">{m}</span></div>)}
          </div>
        </section>

        <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <div className="flex items-center justify-between"><h2 className="font-black">مراسم‌های پیش رو</h2><Link href="/admin/bookings" className="text-xs font-bold text-[#9f6d29]">مشاهده همه</Link></div>
          <div className="mt-4 space-y-3">
            {recentQuotes.slice(0,4).map((q,i)=><div key={q.id} className="flex items-center gap-3 rounded-2xl border border-slate-100 p-3">
              <div className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#fbf3e7] text-[#9f6d29]"><CalendarDays size={20}/></div>
              <div className="min-w-0 flex-1"><b className="block truncate text-sm">{q.type} — {q.name}</b><span className="text-[10px] text-slate-500">{q.date} • {toFaNumber(q.guests)} نفر</span></div>
              <span className={"h-2 w-2 rounded-full "+(i===0?"bg-green-500":"bg-[#b88334]")}/>
            </div>)}
          </div>
        </section>

        <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 className="font-black">تقویم امروز</h2>
          <div className="mt-4 rounded-2xl bg-[#fbf8f2] p-4 text-center">
            <div className="text-xs font-bold text-[#9f6d29]">شهریور ۱۴۰۵</div>
            <div className="mt-3 grid grid-cols-7 gap-1 text-[10px] text-slate-400">{["ش","ی","د","س","چ","پ","ج"].map(x=><span key={x}>{x}</span>)}</div>
            <div className="mt-2 grid grid-cols-7 gap-1 text-xs">{Array.from({length:31},(_,i)=>i+1).map(d=><span key={d} className={"grid aspect-square place-items-center rounded-full "+(d===29?"bg-[#b88334] font-bold text-white":d===22||d===27?"bg-white font-bold text-[#9f6d29]":"")}>{toFaNumber(d)}</span>)}</div>
          </div>
          <div className="mt-4 flex items-center gap-3 rounded-xl border border-green-100 bg-green-50 p-3"><span className="h-2 w-2 rounded-full bg-green-500"/><div><b className="text-xs">مراسم عروسی</b><div className="text-[10px] text-slate-500">۱۹:۰۰ تا ۲۳:۰۰</div></div></div>
        </section>
      </div>

      <div className="mt-4 grid gap-4 xl:grid-cols-[1.25fr_.75fr]">
        <section className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
          <div className="flex items-center justify-between border-b p-5"><h2 className="font-black">آخرین رزروها و استعلام‌ها</h2><Link href="/admin/quotes" className="text-xs font-bold text-[#9f6d29]">مشاهده همه</Link></div>
          <div className="hidden overflow-x-auto md:block"><table className="w-full text-right text-xs"><thead className="bg-slate-50 text-slate-500"><tr>{["#","نام","نوع مراسم","تاریخ","ظرفیت","مبلغ","وضعیت"].map(x=><th className="px-4 py-3 font-medium" key={x}>{x}</th>)}</tr></thead><tbody>{recentQuotes.map((q,i)=><tr key={q.id} className="border-t border-slate-100"><td className="px-4 py-3">{toFaNumber(i+1)}</td><td className="px-4 py-3 font-bold">{q.name}</td><td className="px-4 py-3">{q.type}</td><td className="px-4 py-3">{q.date}</td><td className="px-4 py-3">{toFaNumber(q.guests)} نفر</td><td className="px-4 py-3">{formatPrice(q.total)}</td><td className="px-4 py-3"><Status value={q.status}/></td></tr>)}</tbody></table></div>
          <div className="divide-y md:hidden">{recentQuotes.map(q=><div key={q.id} className="p-4"><div className="flex justify-between gap-3"><b>{q.name}</b><Status value={q.status}/></div><div className="mt-2 flex flex-wrap gap-3 text-[10px] text-slate-500"><span>{q.type}</span><span>{q.date}</span><span>{toFaNumber(q.guests)} نفر</span></div><div className="mt-2 text-xs font-bold text-[#9f6d29]">{formatPrice(q.total)}</div></div>)}</div>
        </section>
        <section className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <div className="flex items-center justify-between"><h2 className="font-black">آخرین پیام‌ها</h2><MessageCircle size={18} className="text-[#b88334]"/></div>
          <div className="mt-4 space-y-1">
            {[["مریم رضایی","سلام، درباره ظرفیت سالن اصلی...","۱۴:۲۰"],["علیرضا نادری","آیا امکان بازدید حضوری این هفته وجود...","۱۲:۴۵"],["الهه کریمی","هزینه منو شام برای ۱۵۰ نفر چقدر است؟","۱۰:۳۰"],["محمد یوسفی","از خدمات عالی شما سپاسگزارم...","دیروز"]].map(([n,m,t])=><div key={n} className="flex gap-3 border-b border-slate-100 py-3 last:border-0"><div className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-slate-100 text-xs font-bold">{n[0]}</div><div className="min-w-0 flex-1"><div className="flex justify-between"><b className="text-xs">{n}</b><span className="text-[9px] text-slate-400">{t}</span></div><p className="mt-1 truncate text-[10px] text-slate-500">{m}</p></div></div>)}
          </div>
        </section>
      </div>
    </div>
  );
}

function Kpi({icon:Icon,label,value,suffix,change,hint}:{icon:typeof Users;label:string;value:string;suffix?:string;change?:string;hint?:string}) {
  return <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><div className="flex items-start justify-between"><div><p className="text-xs text-slate-500">{label}</p><div className="mt-3 text-2xl font-black">{value}{suffix&&<span className="mr-1 text-[10px] font-normal text-slate-500">{suffix}</span>}</div></div><div className="grid h-11 w-11 place-items-center rounded-2xl bg-[#fbf3e7] text-[#b88334]"><Icon size={22}/></div></div>{change?<div className="mt-3 text-[10px] font-bold text-green-600">↑ {change} نسبت به ماه قبل</div>:<div className="mt-3 text-[10px] text-slate-400">{hint}</div>}</div>
}

export function Status({value}:{value:string}) {
  const style = value==="رزرو قطعی"?"bg-green-50 text-green-700":value==="پیش‌فاکتور"?"bg-blue-50 text-blue-700":value==="بازدید"?"bg-purple-50 text-purple-700":value==="لغو شده"?"bg-red-50 text-red-700":"bg-amber-50 text-amber-700";
  return <span className={"inline-block rounded-full px-2.5 py-1 text-[9px] font-bold "+style}>{value}</span>
}
