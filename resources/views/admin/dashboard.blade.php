@extends('layouts.app')
@section('title','داشبورد مدیریت | تالار رویای ماندگار')
@section('content')
@php
$upcoming=$reservations->filter(fn($r)=>$r->event_date && $r->event_date->greaterThanOrEqualTo(today()) && !in_array($r->status,['cancelled','done']))->sortBy('event_date');
$j=\Morilog\Jalali\Jalalian::now();
$jalaliMonthStart=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d',sprintf('%04d/%02d/01',$j->getYear(),$j->getMonth()))->toCarbon();
$nextYear=$j->getMonth()===12?$j->getYear()+1:$j->getYear();
$nextMonth=$j->getMonth()===12?1:$j->getMonth()+1;
$nextJalaliMonth=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d',sprintf('%04d/%02d/01',$nextYear,$nextMonth))->toCarbon();
$paidTotal=\App\Models\Payment::where('status','paid')->where('paid_at','>=',$jalaliMonthStart)->where('paid_at','<',$nextJalaliMonth)->sum('amount');
$reviewCount=\App\Models\Testimonial::where('status','approved')->count();
$ratingAverage=$reviewCount ? round(\App\Models\Testimonial::where('status','approved')->avg('rating')*20) : null;
$months=[];$maxBar=1;
for($i=8;$i>=0;$i--){
 $serial=$j->getYear()*12+$j->getMonth()-1-$i;
 $year=intdiv($serial,12);$month=$serial%12+1;
 $startJ=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d',sprintf('%04d/%02d/01',$year,$month));
 $endYear=$month===12?$year+1:$year;$endMonth=$month===12?1:$month+1;
 $endJ=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d',sprintf('%04d/%02d/01',$endYear,$endMonth));
 $n=\App\Models\Reservation::where('created_at','>=',$startJ->toCarbon())->where('created_at','<',$endJ->toCarbon())->count();
 $months[]=['name'=>$startJ->format('F'),'total'=>$n];
 $maxBar=max($maxBar,$n);
}
$jm=$j->getMonth();$jy=$j->getYear();
$start=\Morilog\Jalali\Jalalian::fromFormat('Y/m/d',sprintf('%04d/%02d/01',$jy,$jm))->toCarbon();
$blank=($start->dayOfWeek+1)%7;
$daysInMonth=$jm<=6?31:($jm<=11?30:(($j->isLeapYear())?30:29));
@endphp
<div class="mw-admin">
  <aside class="mw-admin-sidebar">
    <a class="mw-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/venue/logo.png') }}" alt="لوگوی تالار رویای ماندگار"><b>TALARTO</b><small>تالار رویای ماندگار</small></a>
    <nav class="mw-admin-menu" data-tabs><button type="button" data-tab="dashboard"><span class="mw-menu-mark">⌂</span><span>داشبورد</span></button>
    @foreach(['media'=>'مدیا لایبرری','reservations'=>'رزروها','calendar'=>'تقویم مراسم','visits'=>'بازدیدها','services'=>'خدمات','gallery'=>'گالری تصاویر','testimonials'=>'نظرات مشتریان','contacts'=>'پیام‌ها','settings'=>'تنظیمات','packages'=>'پکیج‌ها','menu'=>'منوی پذیرایی','posts'=>'مقالات','faqs'=>'سوالات متداول'] as $k=>$title)
    <button type="button" data-tab="{{ $k }}"><span class="mw-menu-mark">{{ ['media'=>'▧','reservations'=>'▦','calendar'=>'▤','visits'=>'◷','services'=>'♧','gallery'=>'▧','testimonials'=>'☆','contacts'=>'☏','settings'=>'⚙','packages'=>'◇','menu'=>'♙','posts'=>'▣','faqs'=>'؟'][$k] }}</span><span>{{ $title }}</span></button>
    @endforeach
    <a class="mw-admin-commerce" href="{{ route('admin.commerce') }}">◈ &nbsp; امور مالی و قیمت‌گذاری</a>
    <a class="mw-admin-customer" href="{{ route('home') }}">⌂ &nbsp; مشاهده وب‌سایت</a>
    </nav>
    <div class="mw-admin-sidebar-footer"><span>هر مراسم،</span><b>یک داستان ماندگار...</b><div>♡</div></div>
  </aside>
  <main class="mw-admin-main">
    <div class="mw-admin-toolbar">
      <div class="mw-admin-welcome"><span class="mw-admin-wave">👋</span><b>سلام، مدیر عزیز</b><small>روز خوبی برای خلق خاطره‌های ماندگار است ...</small></div>
      <span class="mw-admin-today">▦ &nbsp; {{ $j->format('l j F Y') }}</span>
      <label class="mw-admin-search"><span>⌕</span><input type="search" placeholder="جستجو در رزروهای اخیر ..." data-admin-search></label>
      <a class="mw-admin-notify" href="#admin-contacts" title="پیام‌های جدید">♧ <i>{{ $contacts->where('status','new')->count() }}</i></a>
      <div class="mw-admin-person"><span class="mw-admin-avatar">م</span><div><b>مدیر تالار</b><small>مدیریت مجموعه</small></div></div>
    </div>
    <div class="mw-admin-dashboard" id="mw-admin-dashboard"><div class="mw-admin-kpis">
      <div class="mw-admin-kpi"><span class="mw-kpi-icon">♙</span><p>استعلام‌های جدید</p><strong>{{ $stats['new']+$stats['visits'] }}</strong><small>رزرو و درخواست بازدید</small></div>
      <div class="mw-admin-kpi"><span class="mw-kpi-icon">✓</span><p>رزروهای قطعی</p><strong>{{ $stats['confirmed'] }}</strong><small>تأییدشده در سامانه</small></div>
      <div class="mw-admin-kpi"><span class="mw-kpi-icon">▦</span><p>مراسم‌های پیش رو</p><strong>{{ $upcoming->count() }}</strong><small>رویدادهای آینده</small></div>
      <div class="mw-admin-kpi"><span class="mw-kpi-icon">≋</span><p>دریافتی ماه جاری</p><strong class="mw-kpi-money">{{ number_format($paidTotal) }}</strong><small>تومان، پرداخت‌های ثبت‌شده</small></div>
      <div class="mw-admin-kpi"><span class="mw-kpi-icon">♡</span><p>رضایت مشتریان</p><strong>{{ $ratingAverage!==null ? $ratingAverage.'٪':'—' }}</strong><small>{{ $reviewCount }} نظر تأییدشده</small></div>
    </div>
    <div class="mw-admin-overview">
      <div class="mw-admin-calendar mw-admin-widget">
        <h2>▦ &nbsp; تقویم امروز</h2>
        <div class="mw-cal-month">{{ $j->format('F Y') }}</div>
        <div class="mw-admin-weekdays">@foreach(['ش','ی','د','س','چ','پ','ج'] as $w)<span>{{ $w }}</span>@endforeach</div>
        <div class="mw-admin-calendar-grid">
        @for($i=0;$i<$blank;$i++)<span></span>@endfor
        @for($day=1;$day<=$daysInMonth;$day++)
          @php $dayString=sprintf('%04d/%02d/%02d',$jy,$jm,$day); $hasEvent=$reservations->contains(fn($r)=>$r->date_jalali===$dayString && !in_array($r->status,['cancelled'])); @endphp
          <span class="{{ $day===$j->getDay() ? 'mw-current-day' : '' }} {{ $hasEvent?'mw-event-day':'' }}">{{ $day }}</span>
        @endfor
        </div>
        <a href="#admin-calendar" class="mw-widget-action" data-select-tab="calendar">مشاهده تقویم کامل ←</a>
      </div>
      <div class="mw-admin-upcoming mw-admin-widget">
        <h2>▦ &nbsp; مراسم‌های پیش رو</h2>
        @forelse($upcoming->take(4) as $r)
          <div class="mw-upcoming-row"><img src="{{ asset('assets/venue/venue-0'.(($loop->index%9)+1).'.webp') }}" alt="تصویر مراسم"><div><b>{{ $r->event_type }} - {{ $r->name }}</b><small>{{ $r->date_jalali }} | {{ $r->guest_count }} نفر</small></div></div>
        @empty <p class="mw-empty-note">هنوز مراسم آینده‌ای ثبت نشده است.</p>@endforelse
        <a href="#admin-reservations" class="mw-widget-action" data-select-tab="reservations">مشاهده همه رزروها ←</a>
      </div>
      <div class="mw-admin-chart mw-admin-widget">
        <h2>▥ &nbsp; نمودار رزروهای ماهانه</h2>
        <p>تعداد درخواست‌های ثبت‌شده در ۹ ماه اخیر</p>
        <div class="mw-chart-bars">
          @foreach($months as $bar)
            <div class="mw-bar"><strong>{{ $bar['total'] }}</strong><i style="height:{{ max(7,round(100*$bar['total']/$maxBar)) }}%" title="{{ $bar['name'] }}: {{ $bar['total'] }} رزرو"></i><small>{{ $bar['name'] }}</small></div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="mw-admin-bottom">
      <div class="mw-admin-messages mw-admin-widget"><h2>☏ &nbsp; آخرین پیام‌ها</h2>
      @forelse($contacts->take(5) as $m)<div class="mw-message-row" data-admin-item><div class="mw-message-avatar">{{ mb_substr($m->name,0,1) }}</div><div><b>{{ $m->name }}</b><p>{{ Str::limit($m->message,70) }}</p></div><small>{{ optional($m->created_at)->format('H:i') }}</small></div>
      @empty <p class="mw-empty-note">پیام جدیدی ثبت نشده است.</p>@endforelse
      </div>
      <div class="mw-admin-recent mw-admin-widget"><h2>▦ &nbsp; آخرین رزروها</h2><a class="mw-widget-action" href="{{ route('admin.commerce.reservations.export') }}">خروجی CSV ↓</a><div class="table-wrap"><table><thead><tr><th>مشتری</th><th>مراسم</th><th>تاریخ</th><th>ظرفیت</th><th>وضعیت</th></tr></thead><tbody>
        @forelse($reservations->take(5) as $r)<tr data-admin-item><td>{{ $r->name }}</td><td>{{ $r->event_type }}</td><td>{{ $r->date_jalali }}</td><td>{{ $r->guest_count }}</td><td><span class="status-pill status-{{ $r->status }}">{{ ['new'=>'جدید','contacted'=>'پیگیری','confirmed'=>'قطعی','cancelled'=>'لغو','done'=>'انجام شده'][$r->status]??$r->status }}</span></td></tr>
        @empty <tr><td colspan="5">رزروی ثبت نشده است.</td></tr> @endforelse
      </tbody></table></div><a href="#admin-reservations" class="mw-widget-action" data-select-tab="reservations">مدیریت رزروها ←</a></div>
    </div>
    </div><section class="mw-admin-management" id="mw-admin-management" hidden><div class="mw-admin-management-head"><span class="mw-eyebrow">مدیریت مجموعه</span><h2>ویرایش و مدیریت اطلاعات</h2><p>از منوی سمت راست بخش مورد نظر را انتخاب کنید.</p></div>
<div class="admin-panel" data-panel="reservations"><h2>درخواست‌های رزرو</h2><div class="table-wrap"><table><tr><th>نام</th><th>موبایل</th><th>مراسم</th><th>تاریخ</th><th>مهمان</th><th>پکیج</th><th>وضعیت</th></tr>@foreach($reservations as $r)<tr><td>{{ $r->name }}</td><td>{{ $r->mobile }}</td><td>{{ $r->event_type }}</td><td>{{ $r->date_jalali }}</td><td>{{ $r->guest_count }}</td><td>{{ $r->package?->title }}</td><td><form class="inline-form" method="post" action="{{ route('admin.reservations.status',$r) }}">@csrf @method('PATCH')<select name="status">@foreach(['new'=>'جدید','contacted'=>'تماس گرفته شد','confirmed'=>'تأیید','cancelled'=>'لغو','done'=>'انجام شد'] as $k=>$v)<option value="{{ $k }}" @selected($r->status===$k)>{{ $v }}</option>@endforeach</select><input name="admin_note" value="{{ $r->admin_note }}" placeholder="یادداشت"><button>ذخیره</button></form></td></tr>@endforeach</table></div></div>
<div class="admin-panel" data-panel="visits" hidden><h2>درخواست‌های بازدید</h2><div class="table-wrap"><table><tr><th>نام</th><th>موبایل</th><th>تاریخ</th><th>مهمان</th><th>وضعیت</th></tr>@foreach($visits as $v)<tr><td>{{ $v->name }}</td><td>{{ $v->mobile }}</td><td>{{ $v->date_jalali }}</td><td>{{ $v->guest_count }}</td><td><form class="inline-form" method="post" action="{{ route('admin.visits.status',$v) }}">@csrf @method('PATCH')<select name="status">@foreach(['new'=>'جدید','contacted'=>'تماس','scheduled'=>'زمان‌بندی','done'=>'انجام شد','cancelled'=>'لغو'] as $k=>$label)<option value="{{ $k }}" @selected($v->status===$k)>{{ $label }}</option>@endforeach</select><input name="admin_note" value="{{ $v->admin_note }}"><button>ذخیره</button></form></td></tr>@endforeach</table></div></div>
<div class="admin-panel" data-panel="calendar" hidden><h2>مدیریت تقویم</h2><form class="admin-form row-form" method="post" action="{{ route('admin.calendar.store') }}">@csrf<input name="date_jalali" class="jalali-input" data-jalali-picker readonly placeholder="تاریخ شمسی" required><select name="status"><option value="booked">رزرو شده</option><option value="unavailable">غیرقابل رزرو</option><option value="special">ویژه</option></select><input name="note" placeholder="یادداشت"><button class="btn">ثبت</button></form><div class="chip-list">@foreach($calendarDates as $d)<form method="post" action="{{ route('admin.calendar.delete',$d) }}">@csrf @method('DELETE')<span>{{ \App\Support\JalaliDate::format($d->date) }} · {{ $d->status }} @if($d->note) · {{ $d->note }} @endif</span><button>×</button></form>@endforeach</div></div>
<div class="admin-panel" data-panel="settings" hidden><h2>تنظیمات سایت</h2><form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.settings') }}">@csrf @method('PUT')<div class="grid2">@foreach(['site_name'=>'نام مجموعه','phone'=>'تلفن','whatsapp'=>'واتساپ','instagram'=>'اینستاگرام','address'=>'آدرس','hero_title'=>'تیتر اصلی','about_title'=>'تیتر درباره ما','capacity_min'=>'حداقل ظرفیت','capacity_max'=>'حداکثر ظرفیت','parking_capacity'=>'ظرفیت پارکینگ','seo_title'=>'عنوان SEO'] as $field=>$label)<label>{{ $label }}<input name="{{ $field }}" value="{{ $settings->$field }}"></label>@endforeach</div><label>متن Hero<textarea name="hero_subtitle">{{ $settings->hero_subtitle }}</textarea></label><div class="grid2"><label>تصویر Hero از URL<input name="hero_image_url" value="{{ $settings->hero_image }}"></label><label>یا آپلود تصویر Hero<input type="file" name="hero_image_file" accept="image/*"><small class="file-hint">حداکثر ۶ مگابایت</small></label></div><label>متن درباره ما<textarea name="about_body" rows="5">{{ $settings->about_body }}</textarea></label><div class="grid2"><label>تصویر درباره ما از URL<input name="about_image_url" value="{{ $settings->about_image }}"></label><label>یا آپلود تصویر<input type="file" name="about_image_file" accept="image/*"></label></div><label>امکانات مجموعه — هر مورد یک خط<textarea name="amenities" rows="6">{{ implode("\n",$settings->amenities??[]) }}</textarea></label><label>توضیح SEO<textarea name="seo_description">{{ $settings->seo_description }}</textarea></label><label>کد iframe نقشه<textarea name="map_embed" rows="4">{{ $settings->map_embed }}</textarea></label><button class="btn">ذخیره تنظیمات</button></form></div>
<div class="admin-panel" data-panel="services" hidden><h2>خدمات و مراسم‌ها</h2><form class="admin-form compact" method="post" enctype="multipart/form-data" action="{{ route('admin.services.store') }}">@csrf<div class="grid2"><input name="title" placeholder="عنوان" required><input name="slug" placeholder="slug"><input name="icon" placeholder="آیکن"><input name="image_url" placeholder="URL تصویر"><input type="file" name="image_file" accept="image/*"><input name="sort_order" type="number" placeholder="ترتیب"></div><textarea name="short_description" placeholder="توضیح کوتاه"></textarea><textarea name="description" placeholder="توضیح کامل"></textarea><label class="check"><input type="checkbox" name="active" value="1" checked> فعال</label><button class="btn">افزودن</button></form>@foreach($services as $s)<details class="edit-card"><summary>{{ $s->title }}</summary><form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.services.update',$s) }}">@csrf @method('PUT')<input name="title" value="{{ $s->title }}"><input name="slug" value="{{ $s->slug }}"><input name="icon" value="{{ $s->icon }}"><input name="image_url" value="{{ $s->image }}"><input type="file" name="image_file" accept="image/*"><textarea name="short_description">{{ $s->short_description }}</textarea><textarea name="description">{{ $s->description }}</textarea><input type="number" name="sort_order" value="{{ $s->sort_order }}"><label><input type="checkbox" name="active" value="1" @checked($s->active)> فعال</label><button class="btn">ذخیره</button></form><form method="post" action="{{ route('admin.services.delete',$s) }}">@csrf @method('DELETE')<button class="danger">حذف</button></form></details>@endforeach</div>
<div class="admin-panel" data-panel="packages" hidden><h2>پکیج‌ها</h2><form class="admin-form" method="post" action="{{ route('admin.packages.store') }}">@csrf<div class="grid2"><input name="title" placeholder="عنوان" required><input name="slug" placeholder="slug"><input name="subtitle" placeholder="زیرعنوان"><input type="number" name="base_price" placeholder="مبلغ پایه"><input type="number" name="per_guest_price" placeholder="قیمت هر مهمان"><input type="number" name="min_guests" placeholder="حداقل مهمان"><input type="number" name="sort_order" placeholder="ترتیب"></div><textarea name="features" placeholder="هر ویژگی در یک خط"></textarea><textarea name="description" placeholder="توضیح"></textarea><label><input type="checkbox" name="featured" value="1"> پیشنهادی</label><label><input type="checkbox" name="active" value="1" checked> فعال</label><button class="btn">افزودن پکیج</button></form>@foreach($packages as $p)<details class="edit-card"><summary>{{ $p->title }} — {{ number_format($p->base_price) }}</summary><form class="admin-form" method="post" action="{{ route('admin.packages.update',$p) }}">@csrf @method('PUT')<input name="title" value="{{ $p->title }}"><input name="slug" value="{{ $p->slug }}"><input name="subtitle" value="{{ $p->subtitle }}"><input type="number" name="base_price" value="{{ $p->base_price }}"><input type="number" name="per_guest_price" value="{{ $p->per_guest_price }}"><input type="number" name="min_guests" value="{{ $p->min_guests }}"><textarea name="features">{{ implode("\n",$p->features??[]) }}</textarea><textarea name="description">{{ $p->description }}</textarea><input name="sort_order" value="{{ $p->sort_order }}"><label><input type="checkbox" name="featured" value="1" @checked($p->featured)> پیشنهادی</label><label><input type="checkbox" name="active" value="1" @checked($p->active)> فعال</label><button class="btn">ذخیره</button></form><form method="post" action="{{ route('admin.packages.delete',$p) }}">@csrf @method('DELETE')<button class="danger">حذف</button></form></details>@endforeach</div>
<div class="admin-panel" data-panel="menu" hidden><h2>منوی پذیرایی</h2><form class="admin-form row-form" method="post" action="{{ route('admin.menu.store') }}">@csrf<input name="category" placeholder="دسته" required><input name="title" placeholder="عنوان" required><input name="description" placeholder="توضیح"><input type="number" name="price_per_guest" placeholder="قیمت نفر"><input type="number" name="sort_order" placeholder="ترتیب"><label><input type="checkbox" name="active" value="1" checked> فعال</label><button class="btn">افزودن</button></form><div class="table-wrap"><table>@foreach($menuItems as $m)<tr><td>{{ $m->category }}</td><td>{{ $m->title }}</td><td>{{ number_format($m->price_per_guest) }}</td><td><form class="inline-form" method="post" action="{{ route('admin.menu.update',$m) }}">@csrf @method('PUT')<input name="category" value="{{ $m->category }}"><input name="title" value="{{ $m->title }}"><input name="description" value="{{ $m->description }}"><input name="price_per_guest" value="{{ $m->price_per_guest }}"><input name="sort_order" value="{{ $m->sort_order }}"><input type="checkbox" name="active" value="1" @checked($m->active)><button>ذخیره</button></form></td><td><form method="post" action="{{ route('admin.menu.delete',$m) }}">@csrf @method('DELETE')<button class="danger">حذف</button></form></td></tr>@endforeach</table></div></div>
@include('admin.partials.media-library')
<div class="admin-panel" data-panel="gallery" hidden><h2>گالری</h2><form class="admin-form row-form" method="post" enctype="multipart/form-data" action="{{ route('admin.gallery.store') }}">@csrf<input name="title" placeholder="عنوان"><input name="category" placeholder="دسته" required><input name="image_url" placeholder="URL تصویر"><input type="file" name="image_file" accept="image/*"><input name="video_url" placeholder="URL ویدیو"><input type="number" name="sort_order" placeholder="ترتیب"><label><input type="checkbox" name="active" value="1" checked> فعال</label><button class="btn">افزودن</button></form><div class="admin-gallery">@foreach($gallery as $g)<div><img src="{{ $g->image }}"><span>{{ $g->title }}</span><form method="post" action="{{ route('admin.gallery.delete',$g) }}">@csrf @method('DELETE')<button class="danger">حذف</button></form></div>@endforeach</div></div>
<div class="admin-panel" data-panel="posts" hidden><h2>مقالات</h2><form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.posts.store') }}">@csrf<input name="title" placeholder="عنوان" required><input name="slug" placeholder="slug"><input name="cover_image_url" placeholder="URL تصویر"><input type="file" name="cover_image_file" accept="image/*"><textarea name="excerpt" placeholder="خلاصه"></textarea><textarea name="body" rows="8" placeholder="متن مقاله" required></textarea><input name="meta_title" placeholder="Meta title"><textarea name="meta_description" placeholder="Meta description"></textarea><label><input type="checkbox" name="published" value="1" checked> منتشر شود</label><button class="btn">افزودن مقاله</button></form>@foreach($posts as $p)<details class="edit-card"><summary>{{ $p->title }}</summary><form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.posts.update',$p) }}">@csrf @method('PUT')<input name="title" value="{{ $p->title }}"><input name="slug" value="{{ $p->slug }}"><input name="cover_image_url" value="{{ $p->cover_image }}"><input type="file" name="cover_image_file" accept="image/*"><textarea name="excerpt">{{ $p->excerpt }}</textarea><textarea name="body" rows="8">{{ $p->body }}</textarea><input name="meta_title" value="{{ $p->meta_title }}"><textarea name="meta_description">{{ $p->meta_description }}</textarea><label><input type="checkbox" name="published" value="1" @checked($p->published_at)> منتشر</label><button class="btn">ذخیره</button></form><form method="post" action="{{ route('admin.posts.delete',$p) }}">@csrf @method('DELETE')<button class="danger">حذف</button></form></details>@endforeach</div>
<div class="admin-panel" data-panel="faqs" hidden><h2>سوالات متداول</h2><form class="admin-form row-form" method="post" action="{{ route('admin.faqs.store') }}">@csrf<input name="question" placeholder="سوال" required><input name="answer" placeholder="پاسخ" required><input name="sort_order" type="number" placeholder="ترتیب"><label><input type="checkbox" name="active" value="1" checked> فعال</label><button class="btn">افزودن</button></form>@foreach($faqs as $f)<form class="admin-form row-form" method="post" action="{{ route('admin.faqs.update',$f) }}">@csrf @method('PUT')<input name="question" value="{{ $f->question }}"><input name="answer" value="{{ $f->answer }}"><input name="sort_order" value="{{ $f->sort_order }}"><label><input type="checkbox" name="active" value="1" @checked($f->active)> فعال</label><button>ذخیره</button></form>@endforeach</div>
<div class="admin-panel" data-panel="testimonials" hidden><h2>نظرات کاربران</h2><div class="table-wrap"><table><tr><th>نام</th><th>مراسم</th><th>امتیاز</th><th>نظر</th><th>وضعیت</th></tr>@foreach($testimonials as $t)<tr><td>{{ $t->name }}</td><td>{{ $t->event_type }}</td><td>{{ $t->rating }}/5</td><td>{{ $t->body }}</td><td><form method="post" action="{{ route('admin.testimonials.status',$t) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()"><option value="pending" @selected($t->status==='pending')>در انتظار</option><option value="approved" @selected($t->status==='approved')>تأیید</option><option value="rejected" @selected($t->status==='rejected')>رد</option></select></form></td></tr>@endforeach</table></div></div>
<div class="admin-panel" data-panel="contacts" hidden><h2>پیام‌های تماس</h2><div class="table-wrap"><table><tr><th>نام</th><th>موبایل</th><th>موضوع</th><th>پیام</th><th>وضعیت</th></tr>@foreach($contacts as $c)<tr><td>{{ $c->name }}</td><td>{{ $c->mobile }}</td><td>{{ $c->subject }}</td><td>{{ $c->message }}</td><td><form method="post" action="{{ route('admin.contacts.status',$c) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()"><option value="new" @selected($c->status==='new')>جدید</option><option value="read" @selected($c->status==='read')>خوانده شد</option><option value="closed" @selected($c->status==='closed')>بسته</option></select></form></td></tr>@endforeach</table></div></div>
</section></main></div>@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const tabs=document.querySelectorAll('.mw-admin-menu [data-tab]');
 const management=document.querySelector('.mw-admin-management');
 const dashboard=document.querySelector('.mw-admin-dashboard');
 function setMode(name){
   const overview=name==='dashboard';
   management.hidden=overview;
   dashboard.hidden=!overview;
   if(!overview) management.scrollIntoView({behavior:'smooth',block:'start'});
 }
 tabs.forEach(btn=>btn.addEventListener('click',()=>setMode(btn.dataset.tab)));
 const requested=(location.hash||'#admin-dashboard').replace('#admin-','');
 const tab=[...tabs].find(t=>t.dataset.tab===requested)||[...tabs].find(t=>t.dataset.tab==='dashboard');
 if(tab){tab.click();setMode(tab.dataset.tab);}
 document.querySelectorAll('[data-select-tab]').forEach(link=>link.addEventListener('click',e=>{e.preventDefault();const btn=[...tabs].find(t=>t.dataset.tab===link.dataset.selectTab);btn?.click()}));
 const search=document.querySelector('[data-admin-search]');
 if(search)search.addEventListener('input',()=>{const v=search.value.trim().toLocaleLowerCase('fa');document.querySelectorAll('[data-admin-item]').forEach(row=>row.hidden=v&&!row.textContent.toLocaleLowerCase('fa').includes(v));});
});
</script>
@endpush
