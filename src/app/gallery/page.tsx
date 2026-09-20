import Image from "next/image";
import SiteHeader from "@/components/SiteHeader";
import SiteFooter from "@/components/SiteFooter";
import PageHero from "@/components/PageHero";
import { gallery } from "@/lib/data";

export const metadata = { title: "گالری" };

export default function GalleryPage() {
  const items = [...gallery, ...gallery.slice(0,4)];
  return (
    <>
      <SiteHeader/>
      <PageHero kicker="GALLERY" title="هر قاب، بخشی از یک داستان عاشقانه" description="گوشه‌هایی از سالن، باغ، دکوراسیون، چیدمان میزها و لحظات ماندگار مراسم در talarto." cta="رزرو بازدید حضوری"/>
      <main className="container-main py-12">
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {items.map((src,i)=><div key={src+i} className={"relative overflow-hidden rounded-3xl bg-[#f6f2eb] "+(i%5===0?"aspect-[4/5] sm:row-span-2":"aspect-[4/3]")}>
            <Image src={src} alt={"گالری تالار " + (i+1)} fill className="object-cover transition duration-500 hover:scale-105" sizes="(max-width:640px) 100vw,(max-width:1024px) 50vw,33vw"/>
          </div>)}
        </div>
      </main>
      <SiteFooter/>
    </>
  );
}
