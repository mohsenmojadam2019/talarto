import Link from "next/link";
import { MapPin, Phone } from "lucide-react";
import BrandIcon from "./BrandIcon";

export default function SiteFooter() {
  return (
    <footer className="mt-24 border-t border-[#eadfce] bg-[#fbf8f2]">
      <div className="container-main grid gap-10 py-12 md:grid-cols-4">
        <div className="md:col-span-2">
          <div className="mb-4 flex items-center gap-3 text-[#b88334]">
            <BrandIcon className="h-12 w-12"/>
            <div><div className="font-serif text-2xl font-bold text-[#17223a]">talarto</div><div className="text-xs">تالار رویای ماندگار</div></div>
          </div>
          <p className="max-w-md text-sm leading-8 text-slate-600">یک شب خاص، یک خاطره ماندگار. از انتخاب منو تا موسیقی و دکور، همه جزئیات مراسم شما را با دقت طراحی می‌کنیم.</p>
        </div>
        <div>
          <h3 className="mb-4 font-bold">دسترسی سریع</h3>
          <div className="space-y-3 text-sm text-slate-600">
            <Link className="block hover:text-[#b88334]" href="/pricing">محاسبه هزینه مراسم</Link>
            <Link className="block hover:text-[#b88334]" href="/gallery">گالری تصاویر</Link>
            <Link className="block hover:text-[#b88334]" href="/services">خدمات تالار</Link>
            <Link className="block hover:text-[#b88334]" href="/admin">پنل مدیریت</Link>
          </div>
        </div>
        <div>
          <h3 className="mb-4 font-bold">ارتباط با ما</h3>
          <div className="space-y-3 text-sm text-slate-600">
            <div className="flex items-center gap-2"><Phone size={16}/><span>۰۲۱-۹۱۰۱۲۳۴۵</span></div>
            <div className="flex items-start gap-2"><MapPin size={16} className="mt-1"/><span>تهران، مجموعه تالار رویای ماندگار</span></div>
            <div className="flex items-center gap-2"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg><span>@talarto</span></div>
          </div>
        </div>
      </div>
      <div className="border-t border-[#eadfce] py-5 text-center text-xs text-slate-500">© ۱۴۰۵ talarto — تمامی حقوق محفوظ است.</div>
    </footer>
  );
}
