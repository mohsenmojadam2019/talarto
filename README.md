# تالارتو

وب‌سایت اختصاصی یک مجموعه تالار و برگزاری مراسم؛ **نه مارکت‌پلیس و نه SaaS**.

## Stack
- Laravel 13
- PHP 8.4
- Blade خالص
- CSS/JS بدون Vite و Node
- SQLite در محیط توسعه
- Morilog/Jalali برای تبدیل و اعتبارسنجی تاریخ شمسی
- IRANSansX-Thin در `public/assets/fonts/IRANSansX-Thin.ttf`

## امکانات
- صفحه اصلی لوکس و روشن RTL
- صفحات عروسی، عقد، نامزدی، تولد، خصوصی و شرکتی
- پکیج‌های مراسم و منوی پذیرایی
- محاسبه‌گر هزینه تقریبی
- تقویم شمسی گرافیکی و جلوگیری از انتخاب تاریخ مسدود/رزرو شده
- درخواست رزرو و درخواست بازدید
- گالری و Lightbox
- نظرات مشتریان
- مجله و SEO، Sitemap، robots و Schema.org EventVenue/FAQ
- تماس، درباره ما و FAQ
- پنل ادمین برای تنظیمات، رزروها، بازدیدها، تقویم، خدمات، پکیج، منو، گالری، مقاله، FAQ و پیام‌ها
- Docker روی PHP 8.4

## اجرا
```bash
docker compose up --build -d
```
سایت: `http://127.0.0.1:8000`

ورود پیش‌فرض ادمین (در `.env` حتماً تغییر دهید):
- `ADMIN_EMAIL=admin@talarto.local`
- `ADMIN_PASSWORD=change-me-now`
