@extends('layouts.app')
@section('title','ساخت حساب مشتریان | تالار رویای ماندگار')
@section('meta_description','ثبت‌نام در پنل مدیریت مراسم تالار رویای ماندگار')
@section('content')
<section class="mw-auth-page" aria-labelledby="register-title">
 <div class="mw-auth-scene" aria-hidden="true"></div>
 <div class="mw-auth-shell">
  <div class="mw-auth-showcase">
   <div class="mw-auth-ornament" aria-hidden="true">♕</div><span class="mw-auth-kicker">آغاز یک خاطره ماندگار</span>
   <h1>برنامه‌ریزی یک جشن<br><em>فراموش‌نشدنی</em></h1>
   <p>حساب خود را بسازید، تاریخ مراسم را انتخاب کنید و تمام مراحل رزرو و پیش‌فاکتور را از یک پنل دنبال کنید.</p>
   <ul class="mw-auth-benefits"><li><span>◇</span> محاسبه دقیق هزینه مراسم</li><li><span>▦</span> رزرو و پیگیری مراحل مراسم</li><li><span>▤</span> مشاهده فاکتور و پرداخت‌ها</li></ul>
  </div>
  <form class="mw-auth-form mw-register-form" method="post" action="{{ route('register.store') }}" aria-labelledby="register-title">
   @csrf
   <div class="mw-form-flourish" aria-hidden="true">❦</div>
   <div class="mw-auth-heading"><span>به رویای ماندگار خوش آمدید</span><h2 id="register-title">ساخت حساب</h2><p>اطلاعات زیر را برای فعال‌سازی پنل وارد کنید.</p></div>
   <label class="mw-auth-field" for="register-name">نام و نام خانوادگی
    <span class="mw-auth-input"><input id="register-name" name="name" autocomplete="name" value="{{ old('name') }}" minlength="2" maxlength="120" required placeholder="نام و نام خانوادگی"><span aria-hidden="true">♙</span></span></label>
   @error('name')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
   <label class="mw-auth-field" for="register-mobile">شماره موبایل
    <span class="mw-auth-input"><input id="register-mobile" name="mobile" type="tel" dir="ltr" inputmode="numeric" autocomplete="tel" pattern="09[0-9]{9}" maxlength="11" value="{{ old('mobile') }}" placeholder="09123456789" required><span aria-hidden="true">♧</span></span></label>
   @error('mobile')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
   <label class="mw-auth-field" for="register-password">رمز عبور
    <span class="mw-auth-input"><input id="register-password" name="password" type="password" autocomplete="new-password" minlength="8" required placeholder="حداقل ۸ کاراکتر"><span aria-hidden="true">♢</span></span></label>
   <label class="mw-auth-field" for="register-confirm">تکرار رمز عبور
    <span class="mw-auth-input"><input id="register-confirm" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required placeholder="تکرار رمز عبور"><span aria-hidden="true">♢</span></span></label>
   @error('password')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
   <button type="submit" class="mw-auth-submit">ساخت حساب و ورود <span aria-hidden="true">←</span></button>
   <p class="mw-auth-switch">قبلاً ثبت‌نام کرده‌اید؟ <a href="{{ route('login') }}">ورود به حساب</a></p>
  </form>
 </div>
 <div class="mw-auth-bottom" aria-hidden="true">♡</div>
</section>
@endsection
