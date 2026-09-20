import Image from "next/image";
import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import PageHero from "@/components/PageHero";
import { gallery } from "@/lib/data";
import { Heart, ShieldCheck, Star, Users } from "lucide-react";

export const metadata = { title: "درباره ما" };

export default function AboutPage() {
  return (
    <>
      <SiteHeader/>
      <PageHero kicker="ABOUT TALARTO" title="یک تالار، برای ساختن خاطره‌ای که تکرار نمی‌شود" description="talarto یک مارکت‌پلیس نیست؛ وب‌سایت رسمی یک مجموعه مراسم است که تمام خدمات، قیمت‌ها، گالری و رزرو را مستقیم مدیریت می‌کند."/>
      <main className="container-main py-14">
        <div className="grid items-center gap-10 lg:grid-cols-2">
          <div className="relative aspect-[4/3] overflow-hidden rounded-[32px]"><Image src={gallery[1]} alt="فضای تالار" fill className="object-cover"/></div>
          <div><h2 className="text-2xl font-black sm:text-3xl">از اولین بازدید تا آخرین آهنگ شب</h2><p className="mt-5 text-sm leading-9 text-slate-600">هدف ما فقط اجاره سالن نیست. طراحی منو، نوشیدنی، موسیقی، دکور، برنامه زمانی و هماهنگی تیم اجرایی در یک مسیر مشخص مدیریت می‌شود تا خانواده‌ها بدون سردرگمی تصمیم بگیرند و هزینه‌ها را از ابتدا شفاف ببینند.</p>
            <div className="mt-7 grid grid-cols-2 gap-3">
              {[[Users,"+۵۰۰","مراسم"],[Star,"۹۸٪","رضایت"],[Heart,"+۱۰ سال","تجربه"],[ShieldCheck,"۲۴/۷","پشتیبانی"]].map(([I,v,t])=>{const C=I as typeof Users;return <div key={String(t)} className="rounded-2xl bg-[#fbf8f2] p-4"><C className="mb-2 text-[#b88334]" size={21}/><b className="text-xl">{String(v)}</b><span className="mr-2 text-xs text-slate-500">{String(t)}</span></div>})}
            </div>
          </div>
        </div>
      </main>
      <SiteFooter/>
    </>
  );
}
