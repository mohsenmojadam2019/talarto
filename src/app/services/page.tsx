import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import PageHero from "@/components/PageHero";
import { Camera, CarFront, ChefHat, Coffee, Flower2, Headphones, PartyPopper, Sparkles, Utensils } from "lucide-react";

const services = [
  [ChefHat,"کترینگ و منوی غذا","دیس‌پرس، سلف سرویس و سلف VIP با امکان شخصی‌سازی آیتم‌ها."],
  [Coffee,"نوشیدنی و بار","نوشیدنی سرد، موکتل، آبمیوه طبیعی، بار گرم و سرویس خوش‌آمدگویی."],
  [Headphones,"DJ و موسیقی","دیجی حرفه‌ای، سیستم صوتی، نورپردازی، کف LED و اجرای زنده."],
  [Flower2,"گل‌آرایی و دکور","طراحی جایگاه، ورودی، میز مهمان، شمع‌آرایی و تم اختصاصی مراسم."],
  [Camera,"عکاسی و فیلمبرداری","تیم حرفه‌ای تصویربرداری، کلیپ فرمالیته و ثبت کامل روز مراسم."],
  [PartyPopper,"افکت‌های مراسم","مه سرد، آتش‌بازی سرد، ورودی ویژه و طراحی لحظات کلیدی جشن."],
  [CarFront,"پارکینگ و تشریفات","پارکینگ اختصاصی، خدمات valet و مدیریت ورود و خروج مهمانان."],
  [Utensils,"پذیرایی ویژه","فینگرفود، دسر بار، میوه و شیرینی و پذیرایی VIP خانواده‌ها."],
  [Sparkles,"مدیریت صفر تا صد","یک مدیر مراسم اختصاصی برای هماهنگی تمام تیم‌ها و زمان‌بندی."],
];

export const metadata = { title: "خدمات تالار" };

export default function ServicesPage() {
  return (
    <>
      <SiteHeader/>
      <PageHero kicker="SERVICES" title="تمام جزئیات مراسم، یکپارچه و حرفه‌ای" description="هر خدمتی که برای یک مراسم کامل نیاز دارید، می‌تواند داخل پکیج قرار بگیرد یا به‌صورت آپشن جداگانه به پیش‌فاکتور اضافه شود." cta="محاسبه هزینه خدمات"/>
      <main className="container-main py-14">
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {services.map(([Icon,title,desc])=>{const C=Icon as typeof ChefHat;return <article key={String(title)} className="soft-card p-6">
            <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fbf3e7] text-[#b88334]"><C size={25}/></div>
            <h2 className="font-black">{String(title)}</h2><p className="mt-3 text-sm leading-7 text-slate-600">{String(desc)}</p>
          </article>})}
        </div>
        <div className="mt-10 rounded-[30px] bg-[#172238] p-7 text-white sm:p-10">
          <div className="max-w-3xl"><h2 className="text-2xl font-black">هر چیزی که انتخاب شود، شفاف به هزینه اضافه می‌شود</h2><p className="mt-3 text-sm leading-8 text-white/65">اگر یک خدمت داخل پکیج انتخابی شما باشد دوباره هزینه نمی‌شود. اگر جداگانه انتخاب شود، مبلغ دقیق آن در محاسبه‌گر و پیش‌فاکتور نمایش داده خواهد شد.</p></div>
        </div>
      </main>
      <SiteFooter/>
    </>
  );
}
