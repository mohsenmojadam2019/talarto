@extends('layouts.app')
@section('title','تالار رویای ماندگار | برگزاری مراسم عروسی در زعفرانیه تهران')
@section('meta_description','تالار رویای ماندگار؛ برگزاری عروسی، عقد و نامزدی با دیزاین لوکس و امکان درخواست بازدید از مجموعه در زعفرانیه تهران.')
@section('content')
<div class="mw-home">
<section class="mw-hero" aria-label="معرفی تالار رویای ماندگار">
  <img class="mw-hero-photo" src="{{ ($siteSettings?->hero_image ?: asset('assets/venue/hero-wedding.webp')) }}" alt="جلوه‌ای از دکوراسیون لوکس مراسم عروسی" fetchpriority="high" />
  <div class="mw-hero-gradient" aria-hidden="true"></div>
  <div class="mw-hero-copy">
    <span class="mw-eyebrow">تــــــالار رویــــــای مانــــــدگار</span>
    <h1>شکوه یک شب <span>عاشقانه</span></h1>
    <div class="mw-heart-line" aria-hidden="true">♡</div>
    <p>برگزاری مراسم عروسی رویایی در فضایی لوکس و خاطره‌انگیز با خدمات کامل و تیم حرفه‌ای، تا زیباترین آغاز زندگی شما به یک خاطره ماندگار تبدیل شود.</p>
    <div class="mw-hero-buttons">
      <a class="mw-gold-button" href="{{ route('reservation') }}">▦ &nbsp; رزرو بازدید حضوری</a>
      <a class="mw-outline-button" href="#wedding-gallery">▷ &nbsp; تماشای تصاویر تالار</a>
    </div>
    <div class="mw-hero-caption">ـــ ♡ ــــ عشق اینجا جشن گرفته می‌شود... ــــ</div>
  </div>
  <span class="mw-script">آغاز یک عمر خوشبختی ...</span>
</section>
<section class="mw-features" aria-label="امکانات ویژه مجموعه">
  @foreach([
    ['♧','تشریفات مراسم','از صفر تا صد با ما'],
    ['✾','دکوراسیون لوکس','مطابق با سلیقه شما'],
    ['▣','عکاسی و فیلمبرداری','ثبت لحظات ماندگار'],
    ['♧','پارکینگ اختصاصی','ظرفیت مناسب و امنیت'],
    ['♫','سیستم صوتی و نورپردازی','تجهیزات مدرن'],
    ['♙','پذیرایی و آشپزی ممتاز','منوی متنوع و باکیفیت']
  ] as $feature)
  <div class="mw-feature"><span class="mw-feature-icon" aria-hidden="true">{{ $feature[0] }}</span><b>{{ $feature[1] }}</b><small>{{ $feature[2] }}</small></div>
  @endforeach
</section>
<section class="mw-about" id="about">
  <div class="mw-about-picture"><img src="{{ ($siteSettings?->about_image ?: asset('assets/venue/venue-01.webp')) }}" alt="فضای شیک و گل‌آرایی تالار عروسی" loading="lazy"><a class="mw-tour-pill" href="{{ route('gallery') }}">▷ &nbsp; گالری و تور تالار</a></div>
  <div class="mw-about-content"><span class="mw-eyebrow">درباره ما | ABOUT US</span><h2>تالار رویای ماندگار</h2>
    <p>ما در تالار رویای ماندگار باور داریم که هر جشن عاشقانه، شایسته بهترین خاطره است. از طراحی دکور و گل‌آرایی تا پذیرایی و هماهنگی مراسم، با دقت و ظرافت در کنار شما هستیم تا روز خاصتان به‌یادماندنی شود.</p>
    <a class="mw-pill-link" href="{{ route('services.index') }}">درباره خدمات بیشتر بدانید &nbsp; ←</a>
  </div>
  <blockquote class="mw-quote"><span>❞</span><p>هر داستان عاشقانه، شایسته یک مکان رویایی است ...</p></blockquote>
</section>
<section class="mw-gallery" id="wedding-gallery">
  <div class="mw-gallery-head"><a href="{{ route('gallery') }}">مشاهده همه تصاویر <span>←</span></a><div><h2>گالری تصاویر</h2><p>تصویرسازی‌هایی از دکور و حال‌وهوای مراسم‌های رویایی</p></div></div>
  <div class="mw-gallery-row" data-mw-gallery-row>
    @foreach($gallery->take(10) as $g)
      <button type="button" class="mw-gallery-image" data-gallery-src="{{ $g->image }}"><img src="{{ $g->image }}" alt="{{ $g->title }}" loading="lazy" /></button>
    @endforeach
  </div>
  <div class="mw-gallery-controls"><button type="button" aria-label="عکس بعدی" data-gallery-shift="1">←</button><button type="button" aria-label="عکس قبلی" data-gallery-shift="-1">→</button></div>
</section>
<section class="mw-proof" aria-label="ویژگی‌های مجموعه">
  <div><span>♡</span><strong>هماهنگی دقیق</strong><small>از بازدید تا برگزاری</small></div>
  <div><span>♙</span><strong>تیم تشریفات</strong><small>همراه شما در مراسم</small></div>
  <div><span>✩</span><strong>پذیرایی حرفه‌ای</strong><small>تجربه‌ای ماندگار</small></div>
  <div><span>♕</span><strong>فضایی لوکس</strong><small>در شمال تهران</small></div>
</section>
<section class="mw-testimonials">
 <div class="mw-section-head"><h2>نظر کسانی که مراسمشان را به ما سپردند</h2><a href="{{ route('review.store') }}" class="mw-quiet-link" onclick="event.preventDefault();document.getElementById('mw-reviews').scrollIntoView({behavior:'smooth'})">ثبت تجربه خود ←</a></div>
 <div class="mw-testimonial-row">
 @forelse($testimonials->take(3) as $t)
 <blockquote><span class="mw-rating">{{ str_repeat('★',(int)$t->rating) }}</span><p>{{ $t->body }}</p><div><b>{{ $t->name }}</b><small>{{ $t->event_type }}</small></div></blockquote>
 @empty <p>اولین تجربه‌تان را با ما در میان بگذارید.</p> @endforelse
 </div>
</section>
<section class="mw-visit-cta" id="visit">
 <div><span class="mw-eyebrow">قبل از تصمیم، از نزدیک ببینید</span><h2>برای بازدید حضوری هماهنگ کنید</h2><p>با هماهنگی قبلی، زیبایی و فضای مجموعه را از نزدیک ببینید و با مشاوران ما درباره جزئیات مراسم گفتگو کنید.</p><a class="mw-gold-button" href="{{ route('reservation') }}">▦ &nbsp; رزرو بازدید حضوری</a></div>
 <img src="{{ asset('assets/venue/venue-07.webp') }}" alt="گل‌آرایی و مسیر مراسم در مجموعه" loading="lazy">
</section>
<section class="mw-review-form" id="mw-reviews"><div><span class="mw-eyebrow">نظر شما</span><h2>تجربه مراسمتان را ثبت کنید</h2><p>نظر شما پس از بررسی مدیریت منتشر می‌شود.</p></div>
<form method="post" action="{{ route('review.store') }}">@csrf<div class="grid2"><input name="name" placeholder="نام شما" required><select name="event_type"><option>عروسی</option><option>عقد</option><option>نامزدی</option><option>تولد</option></select><select name="rating"><option value="5">۵ ستاره</option><option value="4">۴ ستاره</option><option value="3">۳ ستاره</option><option value="2">۲ ستاره</option><option value="1">۱ ستاره</option></select></div><textarea name="body" rows="2" minlength="10" placeholder="نظر شما درباره تجربه برگزاری مراسم..." required></textarea><button class="mw-gold-button" type="submit">ارسال نظر</button></form>
</section>
</div>
<div class="gallery-modal" data-gallery-modal><button type="button" data-gallery-close aria-label="بستن">×</button><img alt="نمای بزرگ تصویر تالار"></div>
@endsection
@push('scripts')
<script>document.querySelectorAll('[data-gallery-shift]').forEach(b=>b.addEventListener('click',()=>document.querySelector('[data-mw-gallery-row]').scrollBy({left:(Number(b.dataset.galleryShift)*260),behavior:'smooth'})));</script>
@endpush
