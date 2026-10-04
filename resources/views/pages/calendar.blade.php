@extends('layouts.app')
@section('title','تقویم تاریخ‌های مراسم | تالارتو')
@section('meta_description','تقویم شمسی تالارتو برای بررسی تاریخ مراسم؛ روزهای رزرو شده و مسدود قابل انتخاب نیستند.')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">تقویم مراسم</span><h1>تاریخ مراسمت را قبل از ثبت درخواست بررسی کن</h1><p>روی کادر تاریخ بزن؛ روزهای رزرو شده یا غیرفعال در تقویم مشخص‌اند و قابل انتخاب نیستند.</p></div></section>
<section class="section"><div class="container split">
<div><span class="eyebrow">تقویم شمسی</span><h2>انتخاب تاریخ</h2><div class="calendar-demo"><input class="jalali-input" data-jalali-picker data-blocked='@json($blockedDates)' readonly placeholder="انتخاب تاریخ شمسی"></div><a class="btn" href="{{ route('reservation') }}">ثبت درخواست رزرو</a></div>
<div class="contact-card"><h3>وضعیت تاریخ‌های ثبت‌شده</h3>
@if($dates->isEmpty())<p>در حال حاضر تاریخ مسدود یا ویژه‌ای ثبت نشده؛ روزهای تقویم قابل استعلام هستند.</p>@else
<div class="calendar-status-list">@foreach($dates as $d)<div class="menu-row"><div><b>{{ \App\Support\JalaliDate::format($d->date) }}</b><span>{{ $d->note ?: 'بدون توضیح' }}</span></div><strong>{{ ['booked'=>'رزرو شده','unavailable'=>'غیرفعال','special'=>'ویژه'][$d->status] ?? $d->status }}</strong></div>@endforeach</div>
@endif
</div></div></section>
@endsection