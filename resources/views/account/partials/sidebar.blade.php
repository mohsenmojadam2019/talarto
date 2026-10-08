<aside class="mw-customer-sidebar" aria-label="منوی پنل مشتری">
 <div class="mw-customer-profile">
  <span class="mw-customer-avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name ?: 'ک',0,1) }}</span>
  <strong>{{ auth()->user()->name }}</strong>
  <span class="mw-customer-phone" dir="ltr">{{ auth()->user()->mobile }}</span>
  <a class="mw-profile-edit" href="{{ route('account.profile') }}">ویرایش پروفایل <span aria-hidden="true">✎</span></a>
 </div>
 <nav class="mw-customer-menu">
  <a href="{{ route('account.dashboard') }}" @class(['active'=>request()->routeIs('account.dashboard')])><span aria-hidden="true">⌂</span> داشبورد</a>
  <a href="{{ route('account.reservations.index') }}" @class(['active'=>request()->routeIs('account.reservations.*') && !request()->routeIs('account.reservations.create')])><span aria-hidden="true">▦</span> رزروهای من</a>
  <a href="{{ route('account.reservations.create') }}" @class(['active'=>request()->routeIs('account.reservations.create')])><span aria-hidden="true">⊕</span> درخواست رزرو جدید</a>
  <a href="{{ route('gallery') }}"><span aria-hidden="true">♡</span> گالری تصاویر</a>
  <a href="{{ route('contact') }}"><span aria-hidden="true">☏</span> پشتیبانی و تماس</a>
  <a href="{{ route('account.profile') }}" @class(['active'=>request()->routeIs('account.profile')])><span aria-hidden="true">⚙</span> تنظیمات حساب</a>
 </nav>
 <div class="mw-customer-side-flower" aria-hidden="true">✽</div>
 <form method="post" action="{{ route('logout') }}" class="mw-customer-signout">@csrf<button type="submit">خروج از حساب <span aria-hidden="true">↩</span></button></form>
</aside>
