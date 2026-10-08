# بازبینی تمام‌عرض پنل مدیریت تالارتو

## اصلاح ساختار

- `/admin` و `/admin/commerce` اکنون از پوسته واحد `mw-admin` و سایدبار راست یکسان استفاده می‌کنند.
- سایدبار مدیریت از فایل `resources/views/admin/partials/sidebar.blade.php` ساخته می‌شود. در داشبورد، دکمه‌های تب محلی حفظ شده‌اند؛ در امور مالی، لینک هر بخش به تب درست داشبورد هدایت می‌کند.
- محدودیت `.container` با عرض ثابت ۱۱۸۰ پیکسل از صفحه امور مالی حذف شد. جای آن را Grid شناور با عرض باقی‌مانده بعد از سایدبار گرفته است.
- فرم‌های بخش مالی در صفحه کوچک تک‌ستونی شده‌اند؛ جدول رزروها درون خودش اسکرول افقی دارد و به صفحه اسکرول افقی تحمیل نمی‌کند.
- در `public/assets/js/app.js` رویداد `hashchange` به تب‌های مدیریت اضافه شده است تا تغییر مستقیم هش آدرس، تب مدنظر را نمایش دهد.

## اعتبارسنجی

- Chrome: عرض‌های ۳۹۰، ۷۶۸، ۱۲۸۰، ۱۶۴۸ و ۱۹۲۰ پیکسل
- برای هر اندازه: `/admin#admin-dashboard`، `/admin#admin-media`، `/admin/commerce`
- اندازه پوسته ادمین برابر عرض viewport، سایدبار متصل به لبه راست، بدون اسکرول افقی کل صفحه
- ۱۴ تب ادمین و ناوبری امور مالی به مدیا لایبرری بررسی شدند
- تست Laravel: `tests/Feature/AdminShellLayoutTest.php` شامل اشتراک سایدبار، درستی لینک‌ها و حفاظت دسترسی مدیر

## اجرای محلی

```bash
cd /home/god/Videos/talarto
docker compose up -d --build
docker run --rm --network none --entrypoint php -v "$PWD:/app" -w /app talarto-app:latest vendor/bin/phpunit --colors=never
python3 local-backups/qa_all_admin_fullwidth.py
```

تست مرورگر صرفاً به‌صورت محلی و بدون انتشار اطلاعات ورود در مخزن نگهداری می‌شود.
