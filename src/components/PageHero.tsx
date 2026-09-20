import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export default function PageHero({kicker,title,description,cta}:{kicker:string;title:string;description:string;cta?:string}) {
  return (
    <section className="border-b border-[#eee4d5] bg-[#fffdfa] py-12 sm:py-16">
      <div className="container-main">
        <div className="section-kicker">{kicker}</div>
        <h1 className="section-title max-w-3xl">{title}</h1>
        <p className="mt-3 max-w-2xl text-sm leading-8 text-slate-600 sm:text-base">{description}</p>
        {cta && <Link href="/pricing" className="btn-gold mt-6">{cta}<ArrowLeft size={17}/></Link>}
      </div>
    </section>
  );
}
