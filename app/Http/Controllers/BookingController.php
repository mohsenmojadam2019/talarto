<?php
namespace App\Http\Controllers;
use App\Models\Venue; use Illuminate\Http\Request;
class BookingController extends Controller{
 public function create(Venue $venue){return view('bookings.create',compact('venue'));}
 public function store(Request $r,Venue $venue){$d=$r->validate(['name'=>'required|string|max:100','mobile'=>['required','regex:/^09\d{9}$/'],'event_type'=>'required|string|max:50','guest_count'=>'required|integer|min:10|max:5000','event_date'=>'required|date|after_or_equal:today','budget'=>'nullable|integer|min:0','message'=>'nullable|string|max:1000']);$venue->bookings()->create($d+['status'=>'new']);return back()->with('success','درخواست شما ثبت شد.');}
}
