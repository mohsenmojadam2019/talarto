<?php
namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class BookingController extends Controller
{
    public function create(Venue $venue)
    {
        return view('bookings.create', compact('venue'));
    }

    public function store(Request $request, Venue $venue)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => ['required','regex:/^09\d{9}$/'],
            'event_type' => 'required|string|max:50',
            'guest_count' => 'required|integer|min:10|max:5000',
            'event_date_jalali' => ['required','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],
            'budget' => 'nullable|integer|min:0',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $eventDate = Jalalian::fromFormat('Y/m/d', $data['event_date_jalali'])->toCarbon()->startOfDay();
        } catch (\Throwable $e) {
            return back()->withErrors(['event_date_jalali' => 'تاریخ شمسی معتبر نیست.'])->withInput();
        }

        if ($eventDate->isBefore(now()->startOfDay())) {
            return back()->withErrors(['event_date_jalali' => 'تاریخ مراسم نمی‌تواند گذشته باشد.'])->withInput();
        }

        unset($data['event_date_jalali']);
        $venue->bookings()->create($data + ['event_date' => $eventDate->toDateString(), 'status' => 'new']);

        return back()->with('success', 'درخواست شما ثبت شد.');
    }
}
