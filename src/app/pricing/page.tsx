import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import QuoteCalculator from "@/components/QuoteCalculator";

export const metadata = { title: "محاسبه هزینه مراسم" };

export default function PricingPage() {
  return (
    <>
      <SiteHeader/>
      <main className="bg-[#fffdfa] py-10 sm:py-14">
        <div className="container-main">
          <div className="mb-8 max-w-3xl">
            <div className="section-kicker">EVENT CALCULATOR</div>
            <h1 className="section-title">هزینه مراسمت را شفاف محاسبه کن</h1>
            <p className="mt-3 text-sm leading-7 text-slate-600">تاریخ جلالی، تعداد مهمان، منوی غذا، نوشیدنی، DJ و خدمات دلخواه را انتخاب کنید. مبلغ به‌صورت لحظه‌ای محاسبه می‌شود.</p>
          </div>
          <QuoteCalculator/>
        </div>
      </main>
      <SiteFooter/>
    </>
  );
}
