@extends('layouts.app')
@section('title','گالری تصاویر | تالار رویای ماندگار')
@section('meta_description','گالری طراحی مراسم عروسی، عقد، نامزدی، تولد و پذیرایی تالار رویای ماندگار')
@section('content')
<section class="page-hero mw-gallery-page-head"><div class="container"><span class="eyebrow">گالری مجموعه</span><h1>جلوه‌هایی از مراسم‌های ماندگار</h1><p>نمونه‌های طراحی و ایده‌های چیدمان سالن، عقد، نامزدی، تولد و پذیرایی</p></div></section>
<section class="mw-gallery-full"><div class="container">
  <div class="mw-gallery-filters" data-gallery-filters>
    <button type="button" class="active" data-gallery-filter="all">همه تصاویر</button>
    @foreach($items->pluck('category')->unique()->filter() as $category)
      <button type="button" data-gallery-filter="{{ $category }}">{{ $category }}</button>
    @endforeach
  </div>
  <p class="mw-gallery-note">تصاویر مفهومی و تولیدشده با هوش مصنوعی، نمونه‌هایی از سبک طراحی هستند و عکس مستند یک محل واقعی محسوب نمی‌شوند.</p>
  <div class="mw-gallery-grid" data-filterable-gallery>
    @foreach($items as $g)
      <button type="button" class="mw-gallery-tile gallery-button" data-gallery-category="{{ $g->category }}" data-gallery-src="{{ $g->image }}" aria-label="نمایش تصویر {{ $g->title }}">
        <img src="{{ $g->image }}" loading="lazy" alt="{{ $g->title }}">
        <span>{{ $g->title }}</span>
      </button>
    @endforeach
  </div>
</div></section>
<div class="gallery-modal" data-gallery-modal><button type="button" data-gallery-close aria-label="بستن">×</button><img alt="نمای بزرگ عکس تالار"></div>
@endsection
@push('scripts')
<script>
(()=>{const menu=document.querySelector('[data-gallery-filters]');if(!menu)return;
menu.addEventListener('click',event=>{const b=event.target.closest('[data-gallery-filter]');if(!b)return;
 menu.querySelectorAll('button').forEach(x=>x.classList.toggle('active',x===b));
 const v=b.dataset.galleryFilter;
 document.querySelectorAll('[data-gallery-category]').forEach(x=>{x.hidden=v!=='all'&&x.dataset.galleryCategory!==v});
});})();
</script>
@endpush
