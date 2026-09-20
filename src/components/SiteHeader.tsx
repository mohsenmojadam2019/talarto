"use client";

import Link from "next/link";
import { CalendarDays, Menu, Phone, X } from "lucide-react";
import { useState } from "react";
import BrandIcon from "./BrandIcon";

const links = [
  ["/", "صفحه اصلی"],
  ["/services", "خدمات"],
  ["/gallery", "گالری"],
  ["/pricing", "محاسبه هزینه"],
  ["/about", "درباره ما"],
  ["/contact", "تماس با ما"],
];

export default function SiteHeader() {
  const [open, setOpen] = useState(false);
  return (
    <header className="sticky top-0 z-50 border-b border-black/5 bg-white/95 backdrop-blur-xl">
      <div className="container-main flex h-[76px] items-center justify-between gap-4">
        <Link href="/" className="flex items-center gap-2 text-[#b88334]">
          <BrandIcon className="h-11 w-11" />
          <div className="leading-tight">
            <div className="font-serif text-2xl font-semibold tracking-wide text-[#162238]">talarto</div>
            <div className="text-[10px] text-[#977247]">تالار رویای ماندگار</div>
          </div>
        </Link>
        <nav className="hidden items-center gap-7 lg:flex">
          {links.map(([href, label]) => (
            <Link key={href} href={href} className="text-sm font-semibold text-[#253047] transition hover:text-[#b88334]">{label}</Link>
          ))}
        </nav>
        <div className="hidden items-center gap-3 md:flex">
          <a href="tel:02191012345" className="flex items-center gap-2 text-xs text-slate-600">
            <Phone size={17} className="text-[#b88334]" />
            <span>۰۲۱-۹۱۰۱۲۳۴۵</span>
          </a>
          <Link href="/pricing" className="btn-gold"><CalendarDays size={17}/> رزرو بازدید</Link>
        </div>
        <button onClick={() => setOpen(!open)} className="rounded-xl border border-slate-200 p-2.5 lg:hidden" aria-label="منو">
          {open ? <X/> : <Menu/>}
        </button>
      </div>
      {open && (
        <div className="border-t bg-white px-5 py-4 lg:hidden">
          <div className="flex flex-col gap-1">
            {links.map(([href, label]) => <Link onClick={() => setOpen(false)} key={href} href={href} className="rounded-xl px-4 py-3 font-semibold hover:bg-[#fbf7ef]">{label}</Link>)}
            <Link onClick={() => setOpen(false)} href="/admin" className="mt-2 rounded-xl bg-[#162238] px-4 py-3 text-center font-semibold text-white">ورود به پنل مدیریت</Link>
          </div>
        </div>
      )}
    </header>
  );
}
