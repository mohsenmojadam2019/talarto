import Link from "next/link";
import Image from "next/image";
import { ArrowLeft, CalendarDays, Camera, CarFront, ChefHat, Gem, Headphones, Heart, Music2, Play, ShieldCheck, Sparkles, Star, Users } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import BrandIcon from "@/components/BrandIcon";
import { gallery } from "@/lib/data";
import { heroSeed } from "@/lib/seed";

const serviceStrip = [
  [Gem, "سالن مجلل", "طراحی مدرن و لوکس"],
  [ChefHat, "کترینگ اختصاصی", "منوهای متنوع و حرفه‌ای"],
  [Sparkles, "دکوراسیون لوکس", "متناسب با سلیقه شما"],
  [CarFront, "پارکینگ اختصاصی", "ظرفیت بالا و امن"],
  [Camera, "آتلیه و تصویربرداری", "ثبت لحظات ماندگار"],
  [Headphones, "سیستم صوتی و DJ", "تجهیزات حرفه‌ای"],
];

export default function HomePage() {
  return (
    <>
      <SiteHeader />
      <main>
        <section className="relative overflow-hidden border-b border-[#eee4d5] bg-[#fffdfa]">
          <div className="absolute inset-0 hero-glow" />
          <div className="container-main relative grid min-h-[610px] items-center gap-8 py-10 lg:grid-cols-[.9fr_1.2fr] lg:py-14">
            <div className="order-2 lg:order-1">
              <div className="mb-5 flex items-center gap-3 text-sm font-bold text-[#aa7629]">
                <span className="h-px w-10 bg-[#c99a4e]" />
                تالار رویای ماندگار
              </div>
              <h1 className="max-w-xl text-[2.4rem] font-black leading-[1.45] tracking-[-.04em] text-[#172238] sm:text-5xl lg:text-[4rem]">
                شکوه یک شب <span className="text-[#9f6d29]">عاشقانه</span>
              </h1>
              <div className="my-5 flex items-center gap-3">
                <span className="h-px w-20 bg-[#d5b071]" /><Heart size={17} className="text-[#b88334]"/><span className="h-px w-20 bg-[#d5b071]" />
              </div>
              <p className="max-w-lg text-sm leading-8 text-slate-600 sm:text-base">
                برگزاری مراسم عروسی رؤیایی در فضایی لوکس و خاطره‌انگیز؛ از منوی پذیرایی و نوشیدنی تا موسیقی، نورپردازی و دکور، همه‌چیز مطابق سلیقه شما.
              </p>
              <div className="mt-7 flex flex-wrap gap-3">
                <Link href="/pricing" className="btn-gold"><CalendarDays size={18}/> محاسبه هزینه و رزرو بازدید</Link>
                <Link href="/gallery" className="btn-outline"><Play size={17}/> تماشای گالری</Link>
              </div>
              <div className="mt-8 flex flex-wrap items-center gap-5 text-xs font-semibold text-slate-500">
                <span className="flex items-center gap-2"><ShieldCheck size={18} className="text-[#b88334]"/> پیش‌فاکتور شفاف</span>
                <span className="flex items-center gap-2"><Users size={18} className="text-[#b88334]"/> ظرفیت تا ۶۵۰ مهمان</span>
                <span className="flex items-center gap-2"><Star size={18} className="text-[#b88334]"/> رضایت ۹۸٪</span>
              </div>
            </div>

            <div className="order-1 relative lg:order-2">
              <div className="relative mx-auto aspect-[1.18/1] max-w-[690px] overflow-hidden rounded-[36px] border-[10px] border-white shadow-[0_30px_100px_rgba(23,34,56,.12)]">
                <Image src={heroSeed.src} alt={heroSeed.alt} fill priority className="object-cover" sizes="(max-width:1024px) 100vw, 58vw"/>
                <div className="absolute inset-0 bg-gradient-to-t from-[#172238]/35 via-transparent to-white/10" />
                <div className="absolute bottom-5 right-5 flex items-center gap-3 rounded-2xl bg-white/95 px-4 py-3 shadow-xl backdrop-blur">
                  <div className="text-[#b88334]"><BrandIcon className="h-10 w-10"/></div>
                  <div><div className="text-sm font-black">یک آغاز، یک عمر خوشبختی</div><div className="mt-1 text-[11px] text-slate-500">در talarto، عشق جشن گرفته می‌شود</div></div>
                </div>
              </div>
              <div className="absolute -bottom-5 left-2 hidden rounded-3xl border border-[#ead9bd] bg-[#fffdf9] p-4 shadow-xl sm:block">
                <div className="flex items-center gap-3">
                  <div className="flex -space-x-3 space-x-reverse">
                    <span className="h-10 w-10 rounded-full border-2 border-white bg-[#ead7b7]"/>
                    <span className="h-10 w-10 rounded-full border-2 border-white bg-[#d8b77f]"/>
                    <span className="h-10 w-10 rounded-full border-2 border-white bg-[#c59a56]"/>
                  </div>
                  <div className="text-xs"><b className="block text-[#172238]">+۵۰۰ مراسم موفق</b><span className="text-slate-500">اعتماد زوج‌های خوشحال</span></div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section className="border-b border-[#eee4d5] bg-white">
          <div className="container-main grid grid-cols-2 divide-x divide-x-reverse divide-[#eee4d5] md:grid-cols-3 lg:grid-cols-6">
            {serviceStrip.map(([Icon, title, sub]) => {
              const C = Icon as typeof Gem;
              return <div key={String(title)} className="flex min-h-32 flex-col items-center justify-center px-3 py-6 text-center">
                <C className="mb-3 text-[#b88334]" size={26}/><b className="text-sm">{String(title)}</b><span className="mt-1 text-[10px] text-slate-500">{String(sub)}</span>
              </div>
            })}
          </div>
        </section>

        <section className="container-main py-20">
          <div className="grid items-center gap-12 lg:grid-cols-2">
            <div className="relative min-h-[470px]">
              <div className="absolute inset-x-0 top-0 h-[380px] overflow-hidden rounded-[30px]">
                <Image src={gallery[0]} fill className="object-cover" alt="سالن عروسی تالارتو" sizes="(max-width:1024px) 100vw, 50vw"/>
              </div>
              <div className="absolute bottom-0 left-4 h-[210px] w-[42%] overflow-hidden rounded-[24px] border-[7px] border-white shadow-2xl">
                <Image src={gallery[2]} fill className="object-cover" alt="میز پذیرایی تالارتو" sizes="240px"/>
              </div>
              <div className="absolute bottom-6 right-5 rounded-2xl bg-white px-5 py-4 shadow-xl">
                <div className="text-2xl font-black text-[#b88334]">+۱۰ سال</div><div className="text-xs text-slate-500">تجربه حرفه‌ای</div>
              </div>
            </div>
            <div>
              <div className="section-kicker">ABOUT US</div>
              <h2 className="section-title">داستان ما؛ شروع یک خاطره ماندگار</h2>
              <div className="my-5 h-px w-24 bg-[#c99a4e]"/>
              <p className="text-sm leading-9 text-slate-600 sm:text-base">ما باور داریم هر عروسی شایسته جشنی فراموش‌نشدنی است. تیم talarto با ترکیب فضای باشکوه، کترینگ حرفه‌ای، دکور اختصاصی و مدیریت دقیق مراسم، تمام جزئیات را از اولین بازدید تا آخرین لحظه جشن همراه شما پیش می‌برد.</p>
              <div className="mt-7 grid gap-3 sm:grid-cols-2">
                {["مشاوره و طراحی مراسم","پیش‌فاکتور شفاف و دقیق","هماهنگی کامل روز مراسم","پشتیبانی اختصاصی خانواده‌ها"].map(x=><div key={x} className="flex items-center gap-2 rounded-xl bg-[#fbf8f2] px-4 py-3 text-sm font-semibold"><ShieldCheck size={17} className="text-[#b88334]"/>{x}</div>)}
              </div>
              <Link href="/about" className="btn-outline mt-7">درباره ما بیشتر بدانید <ArrowLeft size={16}/></Link>
            </div>
          </div>
        </section>

        <section className="bg-[#fbf8f2] py-20">
          <div className="container-main">
            <div className="mb-10 text-center"><div className="section-kicker">GALLERY</div><h2 className="section-title">نگاهی به زیبایی‌ها</h2><p className="mt-2 text-sm text-slate-500">گوشه‌ای از فضای تالار، دکور و لحظات ماندگار</p></div>
            <div className="grid auto-rows-[220px] gap-4 md:grid-cols-4">
              {gallery.slice(0,5).map((img,i)=><div key={img+i} className={"relative overflow-hidden rounded-3xl "+(i===0?"md:col-span-2 md:row-span-2":"")}>
                <Image src={img} fill className="object-cover transition duration-500 hover:scale-105" alt={"گالری تالار "+(i+1)} sizes="(max-width:768px) 100vw, 25vw"/>
              </div>)}
            </div>
            <div className="mt-8 text-center"><Link className="btn-outline" href="/gallery">مشاهده گالری کامل <ArrowLeft size={16}/></Link></div>
          </div>
        </section>

        <section className="container-main py-14">
          <div className="soft-card grid gap-6 px-6 py-8 sm:grid-cols-2 lg:grid-cols-4">
            {[
              ["+۵۰۰","مراسم موفق",Heart],
              ["+۱۰ سال","تجربه حرفه‌ای",Users],
              ["۹۸٪","رضایت مشتریان",Star],
              ["۲۴/۷","پشتیبانی هماهنگی",Music2],
            ].map(([v,t,Icon])=>{const C=Icon as typeof Heart;return <div key={String(t)} className="flex items-center justify-center gap-4 border-[#eadfce] lg:border-l last:border-0"><C className="text-[#b88334]"/><div><div className="text-2xl font-black">{String(v)}</div><div className="text-xs text-slate-500">{String(t)}</div></div></div>})}
          </div>
        </section>

        <section className="container-main pb-8">
          <div className="relative overflow-hidden rounded-[34px] bg-[#172238] px-7 py-12 text-white md:px-12">
            <div className="absolute inset-0 opacity-10 hero-glow"/>
            <div className="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
              <div><div className="text-sm text-[#e5c48e]">برای شب رؤیایی خود آماده‌اید؟</div><h2 className="mt-2 text-2xl font-black sm:text-3xl">هزینه مراسمتان را همین حالا محاسبه کنید</h2><p className="mt-3 text-sm text-white/65">تعداد مهمان، منوی غذا، نوشیدنی، DJ و خدمات دلخواه را انتخاب کنید و برآورد هزینه را شفاف ببینید.</p></div>
              <Link href="/pricing" className="btn-gold shrink-0">شروع محاسبه هزینه <ArrowLeft size={18}/></Link>
            </div>
          </div>
        </section>
      </main>
      <SiteFooter/>
    </>
  );
}
