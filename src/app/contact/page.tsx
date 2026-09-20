"use client";

import { useState } from "react";
import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import PageHero from "@/components/PageHero";
import { CheckCircle2, Clock3, MapPin, Phone } from "lucide-react";

export default function ContactPage() {
  const [sent,setSent]=useState(false);
  return (
    <>
      <SiteHeader/>
      <PageHero kicker="CONTACT" title="برای بازدید حضوری، با ما در تماس باشید" description="کارشناسان تالار برای بررسی تاریخ، ظرفیت و پکیج مناسب مراسم شما پاسخ‌گو هستند."/>
      <main className="container-main grid gap-8 py-14 lg:grid-cols-[.8fr_1.2fr]">
        <div className="space-y-4">
          {[[Phone,"تماس و مشاوره","۰۲۱-۹۱۰۱۲۳۴۵"],[MapPin,"آدرس","تهران، مجموعه تالار رویای ماندگار"],[Clock3,"ساعات پاسخ‌گویی","همه‌روزه از ۱۰ تا ۲۲"]].map(([I,t,v])=>{const C=I as typeof Phone;return <div key={String(t)} className="soft-card flex gap-4 p-5"><div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#fbf3e7] text-[#b88334]"><C size={21}/></div><div><b>{String(t)}</b><p className="mt-1 text-sm leading-7 text-slate-500">{String(v)}</p></div></div>})}
        </div>
        <form onSubmit={e=>{e.preventDefault();setSent(true)}} className="soft-card p-5 sm:p-7">
          <h2 className="text-xl font-black">درخواست تماس</h2>
          <div className="mt-5 grid gap-4 sm:grid-cols-2">
            <label className="text-xs text-slate-500">نام و نام خانوادگی<input required className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label>
            <label className="text-xs text-slate-500">شماره موبایل<input required inputMode="tel" className="mt-2 w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label>
            <label className="text-xs text-slate-500 sm:col-span-2">توضیحات<textarea rows={5} className="mt-2 w-full resize-none rounded-xl border border-slate-200 p-3 outline-none focus:border-[#b88334]"/></label>
          </div>
          <button className="btn-gold mt-5">ثبت درخواست</button>
          {sent && <div className="mt-4 flex items-center gap-2 rounded-xl bg-green-50 p-3 text-sm font-bold text-green-700"><CheckCircle2 size={18}/> درخواست ثبت شد؛ با شما تماس می‌گیریم.</div>}
        </form>
      </main>
      <SiteFooter/>
    </>
  );
}
