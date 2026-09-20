"use client";

import { CalendarDays } from "lucide-react";
import { toFaNumber } from "@/lib/utils";

const months = ["فروردین","اردیبهشت","خرداد","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند"];

export default function JalaliDateField({ value, onChange }: { value: string; onChange: (value: string) => void }) {
  const parts = value ? value.split("/") : ["1405","7","1"];
  const year = parts[0] || "1405";
  const month = parts[1] || "7";
  const day = parts[2] || "1";
  const update = (part: number, next: string) => {
    const data = [year, month, day];
    data[part] = next;
    onChange(data.join("/"));
  };
  return (
    <div className="rounded-2xl border border-[#e7dece] bg-white p-4">
      <div className="mb-3 flex items-center gap-2 text-sm font-bold"><CalendarDays size={18} className="text-[#b88334]"/> تاریخ مراسم (جلالی)</div>
      <div className="grid grid-cols-3 gap-2">
        <label className="text-xs text-slate-500">روز
          <select value={day} onChange={e=>update(2,e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-800 outline-none focus:border-[#c99a4e]">
            {Array.from({length:31},(_,i)=>i+1).map(d=><option key={d} value={d}>{toFaNumber(d)}</option>)}
          </select>
        </label>
        <label className="text-xs text-slate-500">ماه
          <select value={month} onChange={e=>update(1,e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-800 outline-none focus:border-[#c99a4e]">
            {months.map((m,i)=><option key={m} value={i+1}>{m}</option>)}
          </select>
        </label>
        <label className="text-xs text-slate-500">سال
          <select value={year} onChange={e=>update(0,e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-800 outline-none focus:border-[#c99a4e]">
            {[1405,1406,1407].map(y=><option key={y} value={y}>{toFaNumber(y)}</option>)}
          </select>
        </label>
      </div>
    </div>
  );
}
