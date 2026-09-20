"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Bell, CalendarDays, ChevronDown, ClipboardList, Coffee, GalleryHorizontalEnd, Gauge, Menu, MessageCircle, Music2, Settings, UtensilsCrossed, X, Sparkles } from "lucide-react";
import { useState } from "react";
import BrandIcon from "./BrandIcon";
import { todayJalali } from "@/lib/utils";

const nav = [
  ["/admin","داشبورد",Gauge],
  ["/admin/quotes","استعلام‌ها و پیش‌فاکتورها",ClipboardList],
  ["/admin/bookings","رزروها",CalendarDays],
  ["/admin/calendar","تقویم مراسم",CalendarDays],
  ["/admin/food","منو و غذا",UtensilsCrossed],
  ["/admin/drinks","نوشیدنی‌ها",Coffee],
  ["/admin/music","موسیقی و DJ",Music2],
  ["/admin/extras","خدمات جانبی",Sparkles],
  ["/admin/gallery","گالری",GalleryHorizontalEnd],
  ["/admin/messages","پیام‌ها",MessageCircle],
  ["/admin/settings","تنظیمات",Settings],
] as const;

export default function AdminShell({children}:{children:React.ReactNode}) {
  const [open,setOpen]=useState(false);
  const path=usePathname();
  const side = <div className="flex h-full flex-col bg-white">
    <div className="flex h-24 items-center border-b border-[#eee4d5] px-6">
      <Link href="/" className="flex items-center gap-2 text-[#b88334]"><BrandIcon className="h-11 w-11"/><div><b className="font-serif text-xl text-[#172238]">talarto</b><div className="text-[10px]">پنل مدیریت تالار</div></div></Link>
    </div>
    <nav className="flex-1 space-y-1 overflow-y-auto p-4">
      {nav.map(([href,label,Icon])=>{
        const active=href==="/admin"?path===href:path.startsWith(href);
        return <Link key={href} href={href} onClick={()=>setOpen(false)} className={"flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition "+(active?"bg-[#fbf3e7] text-[#9f6d29]":"text-slate-600 hover:bg-slate-50 hover:text-[#172238]")}>
          <Icon size={19}/><span className="flex-1">{label}</span>{label==="پیام‌ها" && <span className="rounded-full bg-[#b88334] px-2 py-.5 text-[10px] text-white">۳</span>}
        </Link>
      })}
    </nav>
    <div className="m-4 rounded-3xl bg-[#172238] p-4 text-white">
      <div className="text-xs text-[#e5c48e]">هر مراسم</div><div className="mt-1 font-bold">یک داستان ماندگار...</div><div className="mt-4 h-px bg-white/10"/>
      <Link href="/" className="mt-3 block text-xs text-white/60 hover:text-white">مشاهده وب‌سایت ←</Link>
    </div>
  </div>;

  return (
    <div className="min-h-screen bg-[#f7f7f8] text-[#172238]">
      <aside className="fixed inset-y-0 right-0 z-40 hidden w-[245px] border-l border-[#e9e6e1] lg:block">{side}</aside>
      {open && <><button className="fixed inset-0 z-40 bg-[#172238]/30 lg:hidden" onClick={()=>setOpen(false)} aria-label="بستن"/><aside className="fixed inset-y-0 right-0 z-50 w-[285px] max-w-[88vw] shadow-2xl lg:hidden">{side}<button onClick={()=>setOpen(false)} className="absolute left-4 top-5 rounded-xl border border-slate-200 bg-white p-2"><X size={18}/></button></aside></>}
      <div className="lg:pr-[245px]">
        <header className="sticky top-0 z-30 flex h-[74px] items-center gap-4 border-b border-[#e9e6e1] bg-white/95 px-4 backdrop-blur sm:px-6">
          <button onClick={()=>setOpen(true)} className="rounded-xl border border-slate-200 p-2.5 lg:hidden"><Menu size={20}/></button>
          <div className="min-w-0 flex-1">
            <div className="text-sm font-black sm:text-base">سلام، مدیر عزیز 👋</div>
            <div className="truncate text-[10px] text-slate-500 sm:text-xs">امروز {todayJalali()} — روز خوبی برای ساختن خاطره‌ای ماندگار است.</div>
          </div>
          <div className="hidden rounded-xl bg-[#fbf8f2] px-4 py-2 text-xs font-bold text-[#9f6d29] md:block"><CalendarDays size={16} className="ml-2 inline"/>{todayJalali()}</div>
          <button className="relative rounded-xl border border-slate-200 p-2.5"><Bell size={19}/><span className="absolute -left-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-[#b88334] text-[9px] text-white">۳</span></button>
          <button className="hidden items-center gap-2 rounded-xl border border-slate-200 p-2 sm:flex"><div className="grid h-8 w-8 place-items-center rounded-full bg-[#172238] text-xs text-white">م</div><span className="text-xs font-bold">مدیر تالار</span><ChevronDown size={15}/></button>
        </header>
        <main className="p-3 sm:p-6">{children}</main>
      </div>
    </div>
  );
}
