@extends('layouts.app')
@section('title','تنظیمات حساب | تالار رویای ماندگار')
@section('content')
<div class="mw-customer-page mw-customer-profile-page">
 <section class="mw-customer-profile-hero"><div><span class="mw-panel-eyebrow">حساب کاربری</span><h1>تنظیمات و مشخصات حساب</h1><p>اطلاعات تماس و رمز عبور حساب خود را از این بخش مدیریت کنید.</p></div></section>
 <div class="mw-customer-workspace">
  @include('account.partials.sidebar')
  <div class="mw-customer-content">
   <div class="mw-profile-form-grid">
    <form class="mw-profile-form" method="post" action="{{ route('account.profile.update') }}">
      @csrf @method('PATCH')
      <div class="mw-profile-form-heading"><span aria-hidden="true">♙</span><div><h2>اطلاعات شخصی</h2><p>نام و ایمیل اختیاری خود را به‌روزرسانی کنید.</p></div></div>
      <label for="profile-name">نام و نام خانوادگی<input id="profile-name" name="name" autocomplete="name" required minlength="2" maxlength="120" value="{{ old('name',$user->name) }}"></label>
      @error('name')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
      <label for="profile-mobile">شماره موبایل (شناسه ورود)<input id="profile-mobile" dir="ltr" value="{{ $user->mobile }}" readonly aria-describedby="profile-mobile-hint"></label>
      <small id="profile-mobile-hint">برای تغییر شماره ورود، با پشتیبانی هماهنگ کنید.</small>
      <label for="profile-email">ایمیل (اختیاری)<input id="profile-email" type="email" name="email" autocomplete="email" maxlength="255" value="{{ old('email',$user->email) }}" placeholder="example@email.com"></label>
      @error('email')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
      <button class="mw-auth-submit" type="submit">ذخیره تغییرات</button>
    </form>
    <form class="mw-profile-form" method="post" action="{{ route('account.profile.password') }}">
      @csrf @method('PUT')
      <div class="mw-profile-form-heading"><span aria-hidden="true">♢</span><div><h2>امنیت حساب</h2><p>برای تغییر رمز، ابتدا رمز فعلی را وارد کنید.</p></div></div>
      <label for="old-password">رمز عبور فعلی<input id="old-password" type="password" name="current_password" autocomplete="current-password" required></label>
      @error('current_password')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
      <label for="new-password">رمز عبور جدید<input id="new-password" type="password" name="password" autocomplete="new-password" minlength="8" required></label>
      <label for="new-password-confirm">تکرار رمز جدید<input id="new-password-confirm" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
      @error('password')<p class="mw-field-error" role="alert">{{ $message }}</p>@enderror
      <button class="mw-auth-submit" type="submit">تغییر رمز عبور</button>
    </form>
   </div>
  </div>
 </div>
</div>
@endsection
