@extends('layouts.app')
@section('title','ورود به پنل مشتریان | تالار رویای ماندگار')
@section('meta_description','ورود امن به پنل رزرو مراسم تالار رویای ماندگار')
@section('content')
<section class="mw-auth-page" aria-labelledby="login-title">
 <div class="mw-auth-scene" aria-hidden="true"></div>
 <div class="mw-auth-shell">
  <div class="mw-auth-showcase">
   <div class="mw-auth-ornament" aria-hidden="true">♕</div>
   <span class="mw-auth-kicker">پنل مشتریان تالار رویای ماندگار</span>
   <h1>مراسمت را از یک پنل<br><em>مدیریت کن</em></h1>
   <p>تاریخ، تعداد مهمان، منو و خدمات را انتخاب کن؛ پیش‌فاکتور، پرداخت‌ها و وضعیت رزرو همیشه در دسترس توست.</p>
   <ul class="mw-auth-benefits">
    <li><span aria-hidden="true">◇</span> قیمت‌گذاری شفاف و ریزفاکتور</li>
    <li><span aria-hidden="true">▦</span> ثبت و ویرایش جزئیات مراسم</li>
    <li><span aria-hidden="true">▤</span> مشاهده پرداخت و مانده حساب</li>
   </ul>
  </div>
  <form class="mw-auth-form" method="post" action="{{ route('login.store') }}" aria-labelledby="login-title">
   @csrf
   <div class="mw-form-flourish" aria-hidden="true">❦</div>
   <div class="mw-auth-heading"><span>خوش آمدید</span><h2 id="login-title">ورود</h2><p>با شماره موبایل ثبت‌شده وارد شوید.</p></div>
   <label class="mw-auth-field" for="login-mobile">شماره موبایل
    <span class="mw-auth-input"><input id="login-mobile" name="mobile" type="tel" dir="ltr" inputmode="numeric" autocomplete="tel" pattern="09[0-9]{9}" maxlength="11" value="{{ old('mobile') }}" placeholder="09123456789" required aria-describedby="login-mobile-help"><span aria-hidden="true">♧</span></span>
   </label>
   <small id="login-mobile-help" class="mw-input-hint">شماره موبایل خود را با ۰۹ وارد کنید.</small>
   @error('mobile')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
   <label class="mw-auth-field" for="login-password">رمز عبور
    <span class="mw-auth-input"><input id="login-password" name="password" type="password" autocomplete="current-password" required minlength="8" placeholder="••••••••"><span aria-hidden="true">♧</span></span>
   </label>
   @error('password')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
   <label class="mw-auth-remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> ورود من را به خاطر بسپار</label>
   <button type="submit" class="mw-auth-submit">ورود به پنل <span aria-hidden="true">←</span></button>
   <div class="mw-auth-divider" aria-hidden="true"></div>
   <p class="mw-auth-switch">حساب ندارید؟ <a href="{{ route('register') }}">ساخت حساب</a></p>
  </form>
 </div>
 <div class="mw-auth-bottom" aria-hidden="true">♡</div>
</section>
@endsection
