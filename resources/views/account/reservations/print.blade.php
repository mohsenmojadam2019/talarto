<!doctype html>
<html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>پیش‌فاکتور {{ $reservation->tracking_code }}</title>
<style>body{font-family:Tahoma,Arial,sans-serif;max-width:840px;margin:30px auto;padding:24px;color:#252525;line-height:1.8}header{display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #333;padding-bottom:18px}h1{font-size:25px}table{width:100%;border-collapse:collapse;margin-top:24px}td,th{border:1px solid #ddd;text-align:right;padding:10px}th{background:#f3f3f3}footer{margin-top:35px;border-top:1px solid #ccc;padding-top:15px;color:#666}.sum{font-size:22px;font-weight:bold}.no-print{margin-bottom:25px}@media print{body{margin:0;max-width:none}.no-print{display:none}}</style>
</head><body>
<div class="no-print"><button onclick="window.print()">چاپ / ذخیره PDF</button><a href="{{ route('account.reservations.show',$reservation) }}">بازگشت به رزرو</a></div>
<header><div><h1>پیش‌فاکتور تالارتو</h1><p>شماره رزرو: {{ $reservation->tracking_code }}</p></div><div>نسخه {{ $quote->version }}<p>تاریخ صدور: {{ $quote->issued_at?->format('Y/m/d H:i') }}</p></div></header>
<p>مشتری: {{ $reservation->name }} | شماره تماس: {{ $reservation->mobile }}</p>
<p>نوع مراسم: {{ $reservation->event_type }} | تاریخ: {{ $reservation->date_jalali }} | سانس: {{ $reservation->time_slot==='day'?'روز':'شب' }} | مهمان: {{ $reservation->guest_count }} نفر</p>
<table><thead><tr><th>شرح</th><th>تعداد</th><th>مبلغ واحد (تومان)</th><th>جمع (تومان)</th></tr></thead><tbody>
@foreach(($quote->snapshot['items']??[]) as $item)
<tr><td>{{ $item['title_snapshot'] }}</td><td>{{ number_format($item['quantity']??1) }}</td><td>{{ number_format($item['unit_price']??0) }}</td><td>{{ number_format($item['total_price']??0) }}</td></tr>
@endforeach
</tbody></table>
<p class="sum">مبلغ کل: {{ number_format($quote->total) }} تومان</p>
<p>وضعیت پیش‌فاکتور: {{ $quote->status==='accepted'?'تأییدشده توسط مشتری':($quote->status==='superseded'?'جایگزین‌شده':'در انتظار تأیید') }}</p>
<footer>این سند پیش‌فاکتور است و به‌تنهایی به معنی قرارداد امضاشده یا رزرو قطعی نیست. شرایط نهایی طبق قرارداد مجموعه تعیین می‌شود.</footer>
</body></html>
