export type PriceMode = "fixed" | "perPerson" | "included";

export type FoodPackage = {
  id: string;
  title: string;
  subtitle: string;
  pricePerPerson: number;
  minGuests: number;
  badge?: string;
  items: string[];
  includedAddonIds?: string[];
};

export type Addon = {
  id: string;
  title: string;
  description: string;
  price: number;
  mode: PriceMode;
  category: "drink" | "music" | "extra";
  includedIn?: string[];
  active?: boolean;
};

export type DatePremiumRule = {
  id: string;
  title: string;
  date?: string;
  month?: number;
  percent: number;
  active: boolean;
};

export const venue = {
  name: "talarto",
  persianName: "تالار رویای ماندگار",
  phone: "۰۲۱-۹۱۰۱۲۳۴۵",
  address: "تهران، مجموعه تالار رویای ماندگار",
  capacity: 650,
  minGuests: 100,
  baseHallFee: 85000000,
  servicePercent: 10,
  taxPercent: 0,
};

export const foodPackages: FoodPackage[] = [
  {
    id: "dish",
    title: "دیس‌پرس کلاسیک",
    subtitle: "پذیرایی رسمی و اقتصادی",
    pricePerPerson: 1200000,
    minGuests: 100,
    includedAddonIds: ["soft"],
    items: ["چلو جوجه زعفرانی", "زرشک پلو با مرغ", "سالاد فصل", "ماست موسیر", "نوشیدنی پایه", "میوه و شیرینی"],
  },
  {
    id: "buffet",
    title: "سلف سرویس ویژه",
    subtitle: "انتخاب محبوب مراسم عروسی",
    pricePerPerson: 1650000,
    minGuests: 150,
    badge: "پیشنهاد تالار",
    includedAddonIds: ["soft"],
    items: ["چلو کباب کوبیده", "جوجه زعفرانی", "باقالی پلو با گوشت", "خورشت انتخابی", "دو نوع سالاد", "دسر", "میوه و شیرینی"],
  },
  {
    id: "vip",
    title: "سلف VIP",
    subtitle: "منوی کامل برای مراسم لوکس",
    pricePerPerson: 2350000,
    minGuests: 180,
    badge: "VIP",
    includedAddonIds: ["soft", "hotbar", "dj"],
    items: ["شیشلیک", "کباب برگ", "جوجه مخصوص", "ماهی", "باقالی پلو با گوشت", "فینگرفود", "سالاد بار", "دسر بار", "میوه و شیرینی"],
  },
];

export const addons: Addon[] = [
  { id: "soft", title: "نوشیدنی پایه", description: "آب معدنی، نوشابه، دوغ و دلستر", price: 160000, mode: "perPerson", category: "drink", active: true },
  { id: "traditional", title: "شربت سنتی", description: "دو مدل شربت سنتی فصل برای پذیرایی", price: 95000, mode: "perPerson", category: "drink", active: true },
  { id: "mocktail", title: "بار سرد و موکتل", description: "موکتل و آبمیوه طبیعی در بدو ورود", price: 230000, mode: "perPerson", category: "drink", active: true },
  { id: "hotbar", title: "بار گرم", description: "چای، نسکافه و قهوه با باریستا", price: 180000, mode: "perPerson", category: "drink", active: true },
  { id: "dj", title: "DJ حرفه‌ای", description: "دیجی اختصاصی مراسم با اجرای کامل", price: 28000000, mode: "fixed", category: "music", active: true },
  { id: "djlight", title: "DJ + سیستم صوتی و نور", description: "سیستم صوتی، رقص نور و اپراتور", price: 48000000, mode: "fixed", category: "music", active: true },
  { id: "ledfloor", title: "کف LED", description: "کف نورپردازی‌شده برای بخش رقص", price: 32000000, mode: "fixed", category: "music", active: true },
  { id: "live", title: "اجرای زنده", description: "بند موسیقی زنده مراسم", price: 95000000, mode: "fixed", category: "music", active: true },
  { id: "flowers", title: "گل‌آرایی ویژه", description: "گل‌آرایی ورودی، جایگاه و میزها", price: 45000000, mode: "fixed", category: "extra", active: true },
  { id: "fingerfood", title: "فینگرفود ورودی", description: "پذیرایی فینگرفود در شروع مراسم", price: 210000, mode: "perPerson", category: "extra", active: true },
  { id: "coldfire", title: "آتش‌بازی سرد و مه", description: "ورودی عروس و داماد و زمان کیک", price: 18000000, mode: "fixed", category: "extra", active: true },
  { id: "sofreh", title: "سفره عقد", description: "سفره عقد کامل با چیدمان اختصاصی", price: 36000000, mode: "fixed", category: "extra", active: true },
  { id: "photo", title: "عکاسی و فیلمبرداری", description: "تیم تصویربرداری و کلیپ مراسم", price: 65000000, mode: "fixed", category: "extra", active: true },
  { id: "viproom", title: "اتاق VIP عروس و داماد", description: "اتاق اختصاصی با پذیرایی ویژه", price: 12000000, mode: "fixed", category: "extra", active: true },
  { id: "valet", title: "پارکینگ VIP و Valet", description: "پارک خودرو و تحویل کلید با نیروی اختصاصی", price: 15000000, mode: "fixed", category: "extra", active: true },
];

export const datePremiumRules: DatePremiumRule[] = [
  { id: "sp-1405-07-29", title: "تاریخ ویژه مهر", date: "1405/7/29", percent: 12, active: true },
  { id: "sp-1405-08-03", title: "تعطیلات مناسبتی", date: "1405/8/3", percent: 15, active: true },
  { id: "high-season-7", title: "فصل پرتقاضا - مهر", month: 7, percent: 5, active: true },
];

export function getDatePremium(date: string) {
  const normalized = date.split("/").map(Number);
  const [year, month, day] = normalized;
  const exact = datePremiumRules.find((rule) => {
    if (!rule.active || !rule.date) return false;
    const [ry, rm, rd] = rule.date.split("/").map(Number);
    return ry === year && rm === month && rd === day;
  });
  if (exact) return exact;
  return datePremiumRules.find((rule) => rule.active && !rule.date && rule.month === month);
}

export const gallery = [
  "https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1400&q=85",
  "https://images.unsplash.com/photo-1507504031003-b417219a0fde?auto=format&fit=crop&w=1400&q=85",
  "https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1400&q=85",
  "https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=85",
  "https://images.unsplash.com/photo-1507504031003-b417219a0fde?auto=format&fit=crop&w=1000&q=80",
  "https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1000&q=80",
];

export const adminNav = [
  ["dashboard", "داشبورد"],
  ["quotes", "استعلام‌ها و پیش‌فاکتورها"],
  ["bookings", "رزروها"],
  ["calendar", "تقویم مراسم"],
  ["pricing", "قوانین قیمت‌گذاری"],
  ["food", "منو و غذا"],
  ["drinks", "نوشیدنی‌ها"],
  ["music", "موسیقی و DJ"],
  ["extras", "خدمات جانبی"],
  ["gallery", "گالری"],
  ["messages", "پیام‌ها"],
  ["settings", "تنظیمات"],
] as const;

export const recentQuotes = [
  { id: "QT-1405-184", name: "سارا احمدی", type: "عروسی", date: "۱۴۰۵/۰۷/۲۹", guests: 300, status: "رزرو قطعی", total: 628000000 },
  { id: "QT-1405-183", name: "امیر حسینی", type: "نامزدی", date: "۱۴۰۵/۰۸/۰۳", guests: 180, status: "پیش‌فاکتور", total: 392000000 },
  { id: "QT-1405-182", name: "نگین محمدی", type: "عروسی", date: "۱۴۰۵/۰۸/۱۰", guests: 250, status: "بازدید", total: 515000000 },
  { id: "QT-1405-181", name: "رضا کریمی", type: "عقد", date: "۱۴۰۵/۰۸/۱۵", guests: 120, status: "تماس گرفته شد", total: 245000000 },
  { id: "QT-1405-180", name: "مینا لطفی", type: "عروسی", date: "۱۴۰۵/۰۸/۲۲", guests: 380, status: "جدید", total: 734000000 },
];

export const formatPrice = (value: number) =>
  new Intl.NumberFormat("fa-IR").format(Math.round(value)) + " تومان";
