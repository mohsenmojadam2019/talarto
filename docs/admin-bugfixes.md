# گزارش اصلاح باگ‌های پنل مدیریت تالارتو

## محدوده اصلاحات

این تغییرات روی شاخه `main` انجام شده و مربوط به مجموعه تک‌تالاری تالارتو است، نه مارکت‌پلیس یا SaaS.

| شناسه ممیزی | محل اصلاح | نتیجه |
|---|---|---|
| T-01 | `CommerceAdminController` و `admin/commerce.blade.php` | صفحه‌بندی ۳۰تایی، جستجو بر اساس مشتری/موبایل/کد و فیلتر وضعیت |
| T-02 | `AdminController::storeGallery/deleteGallery` | ایجاد/حذف هم‌زمان رکورد گالری و مدیا با تراکنش؛ محافظت از تصویر بنر/معرفی |
| T-03 | `MediaLibraryController::importZip` | fallback نام فایل برای بسته ZIP بدون `manifest.json` |
| T-04 | `AdminController` | حفظ slug هنگام ویرایش بدون slug جدید |
| T-05 | `AdminController` | اعتبارسنجی یکتایی slug خدمات، پکیج‌ها و مقالات |
| T-06 | `AdminPanelRedirectMiddleware` و `app.js` | حفظ بخش فعال پس از ثبت/ویرایش و خطاهای فرم |
| T-07 | `app.js` | افزودن نام دسترس‌پذیر برای کنترل‌هایی که label ندارند |
| T-08 | `AdminController::settings` | ظرفیت حداکثر باید >= حداقل باشد |
| T-09 | `AdminController` و `MediaLibraryController` | جلوگیری از خطای کلید تعریف‌نشده هنگام حذف فیلدهای اختیاری |
| T-10 | `bootstrap/app.php` | تقدم بررسی مجوز مدیر نسبت به implicit model binding |
| T-11 | `AdminController::dashboard` و Blade | صفحه‌بندی و جستجوی تمام رزروهای مدیریت، جدا از کارت‌های خلاصه |
| T-12 | `media-library.blade.php` | گزینه جایگزینی بنر هنگام import به‌صورت پیش‌فرض غیرفعال |

### اصلاح اضافی امور مالی
برای جلوگیری از ثبت پرداخت بر اساس پیش‌فاکتور قدیمی، `CommerceAdminController::storePayment` اکنون **آخرین پیش‌فاکتور** را بررسی می‌کند. فرم ثبت پرداخت فقط برای رزروهای مناسب با پیش‌فاکتور پذیرفته‌شده و مانده مثبت نمایش داده می‌شود.

## تست رگرسیون

- فایل: `tests/Feature/AdminRegressionFixesTest.php` شامل بررسی فرم‌های ناقص، ثبات slug، یکتایی slug، ظرفیت، همگام‌سازی گالری و ورود ZIP بدون manifest.
- فایل CI: `.github/workflows/php-tests.yml` برای lint، `view:cache`، بررسی JavaScript و PHPUnit در PHP 8.4.
- داده‌های تست به صورت SQLite در حافظه ذخیره می‌شوند و دیتابیس واقعی مشتریان را تغییر نمی‌دهند.

## وضعیت تحویل

اصلاحات در GitHub ذخیره شده‌اند. اجرای محلی و تأیید سلامت سرویس روی کامپیوتر `god` باید پس از برقراری مجدد اتصال ریموت انجام شود. تا پیش از سبزشدن Workflow و Smoke Test عملی، از اعلام تأیید صددرصدی کل پنل خودداری شود.
