@extends('layouts.app')
@section('title','تقویم تاریخ‌های مراسم | تالارتو')
@section('meta_description','تقویم شمسی تالارتو؛ وضعیت روزهای بسته و سانس‌های روز و شب رزرو شده را بررسی کنید.')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">تقویم مراسم</span><h1>قبل از انتخاب، وضعیت تاریخ و سانس را ببین</h1><p>بسته‌شدن یک سانس، سانس دیگر همان روز را مسدود نمی‌کند. روزهایی که مدیریت کامل بسته کرده باشد در تقویم قابل انتخاب نیستند.</p></div></section>
<section class="section"><div class="container split">
<div><span class="eyebrow">تقویم شمسی</span><h2>بررسی تاریخ</h2><div class="calendar-demo"><input class="jalali-input" data-jalali-picker data-blocked='@json($blockedDates)' readonly placeholder="انتخاب تاریخ شمسی"></div><a class="btn" href="{{ route('reservation') }}">ساخت مراسم و بررسی سانس</a></div>
<div class="contact-card"><h3>سانس‌های قطعی آینده</h3>
@if($bookings->isEmpty())<p>در حال حاضر سانس قطعی ثبت‌شده‌ای در فهرست آینده نیست.</p>@else
<div class="calendar-status-list">@foreach($bookings as $b)<div class="menu-row"><div><b>{{ $b->date_jalali }}</b><span>رزرو قطعی</span></div><strong>سانس {{ $b->time_slot==='day'?'روز':'شب' }}</strong></div>@endforeach</div>
@endif
@if($dates->isNotEmpty())<h3 style="margin-top:28px">روزهای بسته یا ویژه</h3><div class="calendar-status-list">@foreach($dates as $d)<div class="menu-row"><div><b>{{ \App\Support\JalaliDate::format($d->date) }}</b><span>{{ $d->note ?: 'بدون توضیح' }}</span></div><strong>{{ ['booked'=>'بسته','unavailable'=>'غیرفعال','special'=>'ویژه'][$d->status] ?? $d->status }}</strong></div>@endforeach</div>@endif
</div></div></section>
@endsection
