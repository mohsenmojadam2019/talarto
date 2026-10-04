# تالارتو

بازطراحی کامل به Laravel 13 + PHP 8.4 + Blade، بدون Vite و Node.

## امکانات
- RTL روشن و لوکس
- جستجو بر اساس نوع، شهر، محله، ظرفیت، مراسم، بودجه و تخفیف
- جزئیات مجموعه + گالری + امکانات + Schema.org EventVenue
- مقایسه تا ۴ مجموعه
- درخواست رزرو و ثبت لید
- پنل مدیریت و CRUD مجموعه‌ها
- تاریخ جلالی در پنل با morilog/jalali
- سیدر ۱۰ مجموعه متنوع
- Docker روی PHP 8.4

## اجرا
```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

## فونت IRANSansX
CSS از این فایل استفاده می‌کند:
`public/assets/fonts/IRANSansX-Thin.ttf`

فایل مالک پروژه روی سیستم god قرار دارد:
`/home/god/Documents/IranSansX(Pro)/iransansX family/IRANSansX-Thin.ttf`

در زمان این commit، host محلی god در SentinelX به‌علت محدودیت یک host فعال در پلن Free پارک شده بود؛ بنابراین فایل باینری فونت از سیستم محلی قابل خواندن نبود و باید پس از فعال شدن god به مسیر بالا کپی شود.
