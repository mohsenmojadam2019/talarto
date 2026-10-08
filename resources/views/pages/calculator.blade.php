@extends('layouts.app')
@section('title','محاسبه هزینه مراسم | تالارتو')
@section('meta_description','محاسبه آنلاین هزینه بر اساس تاریخ، سانس، تعداد مهمان، پکیج و خدمات پذیرایی.')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">محاسبه هزینه</span><h1>هزینه مراسم را شفاف محاسبه کن</h1><p>همان موتور قیمت‌گذاری رزرو اصلی استفاده می‌شود؛ مبلغ نهایی هنگام ثبت دوباره توسط سرور محاسبه می‌شود.</p></div></section>
<section class="section alt"><div class="container calculator-wrap"><div><span class="eyebrow">برآورد آنلاین</span><h2>ریز هزینه مراسم</h2><p data-public-price-state>تاریخ و پکیج را انتخاب کنید.</p><div data-public-price-lines></div><div class="calc-result"><span>جمع برآوردی</span><strong data-public-price-result>—</strong><small>تومان</small></div><p class="calc-note">مبلغ در زمان درخواست رزرو مجدداً محاسبه و در پیش‌فاکتور ذخیره می‌شود.</p><a class="btn" href="{{ route('reservation') }}">ادامه برای ثبت رزرو</a></div>
<form class="calculator" data-public-calculator data-preview-url="{{ route('public.price-preview') }}">
@csrf
<label>نوع مراسم<select name="event_type"><option>عروسی</option><option>عقد</option><option>نامزدی</option><option>تولد</option><option>مراسم خصوصی</option><option>مراسم شرکتی</option></select></label>
<label>تاریخ شمسی<input name="event_date_jalali" class="jalali-input" data-jalali-picker readonly required placeholder="انتخاب تاریخ"></label>
<label>سانس<select name="time_slot"><option value="night">شب</option><option value="day">روز</option></select></label>
<label>پکیج<select name="package_id" required><option value="">یک پکیج انتخاب کنید</option>@foreach($packages as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></label>
<label>تعداد مهمان<input name="guest_count" type="number" min="10" max="{{ $siteSettings?->capacity_max ?: 5000 }}" value="100" required></label>
<label>آیتم‌های اضافه منو<select name="menu_items[]" multiple size="6">@foreach($menuItems as $m)<option value="{{ $m->id }}">{{ $m->title }} — {{ number_format($m->price_per_guest) }} تومان/نفر</option>@endforeach</select></label>
<button type="submit" class="btn full">محاسبه دقیق</button>
</form></div></section>
@endsection
