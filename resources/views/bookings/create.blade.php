@extends('layouts.app')
@section('title','درخواست رزرو '.$venue->name)
@section('content')
<section class="page">
  <div class="container narrow">
    <span class="eyebrow">استعلام رایگان</span>
    <h1>درخواست رزرو {{ $venue->name }}</h1>
    <p>تاریخ شمسی، تعداد مهمان و بودجه را وارد کنید.</p>
  </div>
</section>
<section class="section">
  <div class="container narrow">
    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif

    <form method="post" class="form">@csrf
      <div class="grid2">
        <label>نام
          <input name="name" value="{{ old('name') }}" required>
        </label>
        <label>موبایل
          <input name="mobile" value="{{ old('mobile') }}" inputmode="numeric" placeholder="0912..." required>
        </label>
        <label>نوع مراسم
          <select name="event_type">
            @foreach(['عروسی','عقد','نامزدی','تولد','همایش'] as $e)
              <option @selected(old('event_type')===$e)>{{ $e }}</option>
            @endforeach
          </select>
        </label>
        <label>تعداد مهمان
          <input type="number" name="guest_count" value="{{ old('guest_count') }}" required>
        </label>
        <label>تاریخ مراسم به شمسی
          <input name="event_date_jalali" value="{{ old('event_date_jalali') }}" inputmode="numeric" placeholder="۱۴۰۵/۰۸/۲۵" dir="ltr" required>
          <small>فرمت: ۱۴۰۵/۰۸/۲۵ — در دیتابیس به تاریخ استاندارد تبدیل می‌شود.</small>
        </label>
        <label>بودجه تقریبی
          <input type="number" name="budget" value="{{ old('budget') }}">
        </label>
      </div>
      <label>توضیحات
        <textarea name="message" rows="4">{{ old('message') }}</textarea>
      </label>
      <button class="btn">ثبت درخواست</button>
    </form>
  </div>
</section>
@endsection
