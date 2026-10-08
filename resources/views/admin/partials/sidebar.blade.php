<aside class="mw-admin-sidebar" aria-label="منوی مدیریت">
  <a class="mw-admin-logo" href="{{ route('admin.dashboard') }}" aria-label="داشبورد مدیریت تالار">
    <img src="{{ asset('assets/venue/logo.png') }}" alt="لوگوی تالار رویای ماندگار">
    <b>TALARTO</b><small>تالار رویای ماندگار</small>
  </a>
  <nav class="mw-admin-menu" @unless($isCommerce??false) data-tabs @endunless>
    @if($isCommerce??false)
      <a href="{{ route('admin.dashboard') }}#admin-dashboard"><span class="mw-menu-mark">⌂</span><span>داشبورد</span></a>
    @else
      <button type="button" data-tab="dashboard"><span class="mw-menu-mark">⌂</span><span>داشبورد</span></button>
    @endif
    @foreach(['media'=>'مدیا لایبرری','reservations'=>'رزروها','calendar'=>'تقویم مراسم','visits'=>'بازدیدها','services'=>'خدمات','gallery'=>'گالری تصاویر','testimonials'=>'نظرات مشتریان','contacts'=>'پیام‌ها','settings'=>'تنظیمات','packages'=>'پکیج‌ها','menu'=>'منوی پذیرایی','posts'=>'مقالات','faqs'=>'سوالات متداول'] as $k=>$title)
      @if($isCommerce??false)
        <a href="{{ route('admin.dashboard') }}#admin-{{ $k }}"><span class="mw-menu-mark">{{ ['media'=>'▧','reservations'=>'▦','calendar'=>'▤','visits'=>'◷','services'=>'♧','gallery'=>'▧','testimonials'=>'☆','contacts'=>'☏','settings'=>'⚙','packages'=>'◇','menu'=>'♙','posts'=>'▣','faqs'=>'؟'][$k] }}</span><span>{{ $title }}</span></a>
      @else
        <button type="button" data-tab="{{ $k }}"><span class="mw-menu-mark">{{ ['media'=>'▧','reservations'=>'▦','calendar'=>'▤','visits'=>'◷','services'=>'♧','gallery'=>'▧','testimonials'=>'☆','contacts'=>'☏','settings'=>'⚙','packages'=>'◇','menu'=>'♙','posts'=>'▣','faqs'=>'؟'][$k] }}</span><span>{{ $title }}</span></button>
      @endif
    @endforeach
    <a @class(['mw-admin-commerce','active'=>$isCommerce??false]) href="{{ route('admin.commerce') }}"><span class="mw-menu-mark">◈</span><span>امور مالی و قیمت‌گذاری</span></a>
    <a class="mw-admin-customer" href="{{ route('home') }}"><span class="mw-menu-mark">↗</span><span>مشاهده وب‌سایت</span></a>
  </nav>
  <div class="mw-admin-sidebar-footer"><span>هر مراسم،</span><b>یک داستان ماندگار...</b><div>♡</div></div>
</aside>
