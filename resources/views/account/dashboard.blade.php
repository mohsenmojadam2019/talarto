@extends('layouts.app')
@section('title','داشبورد مشتری | تالار رویای ماندگار')
@section('content')
@php
  $total=$reservations->count();
  $upcoming=$reservations->filter(fn ($r)=> !in_array($r->status,['done','cancelled'],true) && $r->event_date && $r->event_date->greaterThanOrEqualTo(today()))->count();
  $finished=$reservations->where('status','done')->count();
  $pending=$reservations->whereIn('status',['new','contacted'])->count();
  $statusNames=['new'=>'در انتظار بررسی','contacted'=>'در حال هماهنگی','confirmed'=>'رزرو قطعی','done'=>'برگزار شده','cancelled'=>'لغو شده'];
@endphp
<div class="mw-customer-page">
 <section class="mw-customer-cover" aria-labelledby="customer-welcome">
  <div class="mw-customer-cover-image" aria-hidden="true"></div>
  <div class="mw-customer-cover-content">
   <span class="mw-cover-kicker">✧ &nbsp; به پنل کاربری خود خوش آمدید</span>
   <h1 id="customer-welcome">{{ auth()->user()->name }}، خوش آمدید</h1>
   <h2>به خانواده رویای ماندگار خوش آمدید</h2>
   <p>اینجا می‌توانید رزروهای خود را پیگیری کنید، درخواست جدید ثبت کنید و جزئیات مراسمتان را با آرامش مدیریت کنید.</p>
  </div>
 </section>
 <div class="mw-customer-workspace">
  @include('account.partials.sidebar')
  <div class="mw-customer-content">
   <div class="mw-customer-stats" aria-label="خلاصه رزروهای شما">
    <div class="mw-customer-stat"><span class="mw-stat-icon" aria-hidden="true">▦</span><span>رزروهای من</span><strong>{{ \App\Support\JalaliDate::display($total) }}</strong><small>مجموع رزروها</small></div>
    <div class="mw-customer-stat"><span class="mw-stat-icon" aria-hidden="true">▣</span><span>رزروهای پیش رو</span><strong>{{ \App\Support\JalaliDate::display($upcoming) }}</strong><small>مراسم‌های آینده</small></div>
    <div class="mw-customer-stat"><span class="mw-stat-icon" aria-hidden="true">✓</span><span>رزروهای برگزارشده</span><strong>{{ \App\Support\JalaliDate::display($finished) }}</strong><small>با موفقیت انجام شده</small></div>
    <div class="mw-customer-stat"><span class="mw-stat-icon" aria-hidden="true">◷</span><span>در انتظار پیگیری</span><strong>{{ \App\Support\JalaliDate::display($pending) }}</strong><small>درخواست‌های در حال بررسی</small></div>
   </div>
   <section class="mw-customer-reservations" aria-labelledby="customer-reservations-title">
    <header class="mw-customer-panel-head">
      <div><span class="mw-panel-eyebrow">مراسم‌های اختصاصی شما</span><h2 id="customer-reservations-title">رزروهای من <span aria-hidden="true">▦</span></h2><p>وضعیت رزرو، تاریخ مراسم و اطلاعات مالی هر درخواست را از اینجا مدیریت کنید.</p></div>
      <a class="mw-customer-add" href="{{ route('account.reservations.create') }}">＋ &nbsp; ثبت درخواست رزرو جدید</a>
    </header>
    @if($reservations->isNotEmpty())
    <div class="mw-customer-table-wrap">
      <table class="mw-customer-table">
       <thead><tr><th>ردیف</th><th>مجموعه</th><th>تاریخ مراسم</th><th>تعداد مهمان</th><th>وضعیت</th><th>مبلغ قرارداد</th><th>عملیات</th></tr></thead>
       <tbody>
        @foreach($reservations->take(8) as $r)
         <tr>
          <td>{{ \App\Support\JalaliDate::display($loop->iteration) }}</td>
          <td><div class="mw-customer-venue"><img src="{{ asset('assets/venue/venue-06.webp') }}" alt="فضای تالار رویای ماندگار" loading="lazy"><div><b>تالار رویای ماندگار</b><small>تهران، زعفرانیه · {{ $r->event_type }}</small><small class="mw-customer-tracking">کد پیگیری: {{ $r->tracking_code }}</small></div></div></td>
          <td><time datetime="{{ $r->event_date?->toDateString() }}">{{ \App\Support\JalaliDate::display($r->date_jalali ?: \App\Support\JalaliDate::format($r->event_date)) }}</time><small>{{ $r->time_slot==='day'?'سانس روز':'سانس شب' }}</small></td>
          <td>{{ \App\Support\JalaliDate::display(number_format($r->guest_count)) }} <small>نفر</small></td>
          <td><span class="mw-customer-status mw-status-{{ $r->status }}">{{ $statusNames[$r->status]??$r->status }}</span></td>
          <td><strong>{{ \App\Support\JalaliDate::display(number_format($r->final_price)) }}</strong><small>تومان</small></td>
          <td><a class="mw-customer-details" href="{{ route('account.reservations.show',$r) }}">مشاهده جزئیات <span aria-hidden="true">◉</span></a></td>
         </tr>
        @endforeach
       </tbody>
      </table>
    </div>
    <a class="mw-customer-all" href="{{ route('account.reservations.index') }}">مشاهده همه رزروها ←</a>
    @else
      <div class="mw-customer-empty"><span aria-hidden="true">♡</span><h3>هنوز مراسمی ثبت نکرده‌اید</h3><p>با انتخاب تاریخ و جزئیات مراسم، برنامه‌ریزی جشن خود را شروع کنید.</p><a href="{{ route('account.reservations.create') }}">ثبت اولین مراسم</a></div>
    @endif
   </section>
   @if($active)
    <section class="mw-customer-next" aria-labelledby="next-event-heading">
     <div><span class="mw-panel-eyebrow">پیگیری مراسم</span><h2 id="next-event-heading">مراسم پیش روی شما</h2><p>{{ $active->event_type }} در تاریخ {{ \App\Support\JalaliDate::display($active->date_jalali ?: \App\Support\JalaliDate::format($active->event_date)) }}</p></div>
     <div class="mw-customer-next-finance"><span>پرداخت‌شده <strong>{{ \App\Support\JalaliDate::display(number_format($active->paid_amount)) }} تومان</strong></span><span>مانده حساب <strong>{{ \App\Support\JalaliDate::display(number_format($active->balance)) }} تومان</strong></span></div>
     <a href="{{ route('account.reservations.show',$active) }}">مشاهده پیش‌فاکتور و جزئیات ←</a>
    </section>
   @endif
  </div>
 </div>
</div>
@endsection
