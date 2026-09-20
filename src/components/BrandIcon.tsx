export default function BrandIcon({ className = "h-10 w-10" }: { className?: string }) {
  return (
    <svg viewBox="0 0 120 120" className={className} fill="none" aria-label="Talarto">
      <path d="M60 12c9 7 13 16 12 26-5-3-9-4-12-4s-7 1-12 4c-1-10 3-19 12-26Z" fill="currentColor" opacity=".9"/>
      <path d="M38 41C25 41 15 52 15 66s10 25 23 25c11 0 19-7 24-17-5-6-8-13-8-21-4-7-9-12-16-12Z" stroke="currentColor" strokeWidth="7"/>
      <path d="M82 41c13 0 23 11 23 25S95 91 82 91c-11 0-19-7-24-17 5-6 8-13 8-21 4-7 9-12 16-12Z" stroke="currentColor" strokeWidth="7"/>
      <path d="M33 101c8-7 17-10 27-10s19 3 27 10M60 34V22" stroke="currentColor" strokeWidth="5" strokeLinecap="round"/>
      <path d="M54 15h12" stroke="currentColor" strokeWidth="4" strokeLinecap="round"/>
    </svg>
  );
}
