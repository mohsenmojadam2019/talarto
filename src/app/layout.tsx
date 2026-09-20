import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: { default: "talarto | تالار رویای ماندگار", template: "%s | talarto" },
  description: "وب‌سایت رسمی تالار رویای ماندگار؛ معرفی خدمات، منوها، گالری و محاسبه آنلاین هزینه مراسم.",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="fa" dir="rtl">
      <body>{children}</body>
    </html>
  );
}
