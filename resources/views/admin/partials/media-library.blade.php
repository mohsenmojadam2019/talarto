<div class="admin-panel mw-media-panel" data-panel="media" hidden>
  <div class="mw-media-head"><div><span class="mw-eyebrow">کتابخانه تصاویر</span><h2>مدیا لایبرری تالار</h2><p>آپلود، دسته‌بندی و انتخاب تصاویر بنر و گالری</p></div><b>{{ $mediaAssets->count() }} تصویر</b></div>
  <div class="mw-media-form-grid">
    <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mw-media-upload">@csrf
      <h3>آپلود چند تصویر</h3><label>فایل‌های JPG / PNG / WebP<input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required></label>
      <label>دسته‌بندی<select name="category" required>@foreach(['سالن عروسی','تولد','عقد','نامزدی','پذیرایی و غذا','دکوراسیون','محوطه'] as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></label>
      <label>عنوان اختیاری<input name="title" maxlength="150" placeholder="سالن اصلی"></label>
      <label class="check"><input type="checkbox" name="is_active" value="1" checked> انتشار در گالری</label><button class="btn" type="submit">آپلود تصاویر</button>
    </form>
    <form method="post" action="{{ route('admin.media.import') }}" enctype="multipart/form-data" class="mw-media-upload">@csrf
      <h3>ورود بسته عکس ZIP</h3><label>انتخاب ZIP<input type="file" name="archive" accept=".zip,application/zip" required></label>
      <label>دسته‌بندی<select name="category" required>@foreach(['سالن عروسی','تولد','عقد','نامزدی','پذیرایی و غذا','دکوراسیون','محوطه'] as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></label>
      <label class="check"><input type="checkbox" name="replace_demo" value="1" checked> جایگزین‌کردن تصاویر نمونه فعلی و تغییر بنر</label><p>حداکثر ۲۰ تصویر. تصاویر تولیدشده صرفاً نمونه طراحی هستند و نباید به‌عنوان عکس واقعی مجموعه معرفی شوند.</p><button class="btn" type="submit">ورود ZIP</button>
    </form>
  </div>
  <div class="mw-media-cards">
    @forelse($mediaAssets as $media)
    <article class="mw-media-card">
      <img src="{{ $media->url }}" loading="lazy" alt="{{ $media->alt_text ?: $media->title }}">
      <div class="mw-media-meta"><b>{{ $media->title }}</b><small>{{ $media->category }} · {{ $media->source==='concept_visual'?'تصویرسازی نمونه':'آپلود شده' }} · {{ $media->width ?: '—' }}×{{ $media->height ?: '—' }}</small></div>
      <details class="mw-media-edit"><summary>ویرایش و مدیریت</summary>
        <form method="post" action="{{ route('admin.media.update',$media) }}">@csrf @method('PATCH')
          <label>عنوان<input name="title" value="{{ $media->title }}" required maxlength="150"></label>
          <label>متن جایگزین<input name="alt_text" value="{{ $media->alt_text }}" maxlength="220"></label>
          <label>دسته<select name="category">@foreach(['سالن عروسی','تولد','عقد','نامزدی','پذیرایی و غذا','دکوراسیون','محوطه'] as $category)<option value="{{ $category }}" @selected($media->category===$category)>{{ $category }}</option>@endforeach</select></label>
          <label>ترتیب<input type="number" name="sort_order" min="0" value="{{ $media->sort_order }}" required></label>
          <label class="check"><input type="checkbox" name="active" value="1" @checked($media->active)> فعال</label><button class="btn" type="submit">ذخیره</button>
        </form>
        <div class="mw-media-actions">
          <form method="post" action="{{ route('admin.media.hero',$media) }}">@csrf<button type="submit">بنر اصلی</button></form>
          <form method="post" action="{{ route('admin.media.about',$media) }}">@csrf<button type="submit">تصویر معرفی</button></form>
          <form method="post" action="{{ route('admin.media.delete',$media) }}" onsubmit="return confirm('تصویر حذف شود؟')">@csrf @method('DELETE')<button class="danger" type="submit">حذف</button></form>
        </div>
      </details>
    </article>
    @empty<p class="mw-empty-note">تصویری ثبت نشده است.</p>@endforelse
  </div>
</div>