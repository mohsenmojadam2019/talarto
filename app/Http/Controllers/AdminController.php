<?php
namespace App\Http\Controllers;
use App\Models\Venue; use App\Models\Booking; use Illuminate\Http\Request; use Illuminate\Support\Str;
class AdminController extends Controller{
 public function login(){return view('admin.login');}
 public function authenticate(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required']);if(!hash_equals((string)config('app.admin_email'),$d['email'])||!hash_equals((string)config('app.admin_password'),$d['password']))return back()->withErrors(['email'=>'اطلاعات ورود نادرست است.']);$r->session()->regenerate();$r->session()->put('admin_authenticated',true);return redirect()->route('admin.dashboard');}
 public function dashboard(){return view('admin.dashboard',['venueCount'=>Venue::count(),'bookingCount'=>Booking::count(),'newBookingCount'=>Booking::where('status','new')->count(),'bookings'=>Booking::with('venue')->latest()->take(20)->get()]);}
 public function venues(){return view('admin.venues',['venues'=>Venue::latest()->paginate(30)]);}
 public function storeVenue(Request $r){Venue::create($this->payload($r));return back()->with('success','مجموعه ثبت شد.');}
 public function updateVenue(Request $r,Venue $venue){$venue->update($this->payload($r));return back()->with('success','ذخیره شد.');}
 public function destroyVenue(Venue $venue){$venue->delete();return back();}
 public function logout(Request $r){$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('admin.login');}
 private function payload(Request $r):array{$d=$r->validate(['name'=>'required|string|max:180','slug'=>'nullable|string|max:180','type'=>'required|string','city'=>'required|string','district'=>'nullable|string','address'=>'nullable|string','capacity_min'=>'nullable|integer','capacity_max'=>'nullable|integer','price_from'=>'nullable|integer','price_to'=>'nullable|integer','short_description'=>'nullable|string','description'=>'nullable|string','cover_image'=>'nullable|url','discount_percent'=>'nullable|integer|min:0|max:100','meta_title'=>'nullable|string|max:180','meta_description'=>'nullable|string|max:320']);$d['slug']=$d['slug']?:Str::slug($d['name']).'-'.Str::lower(Str::random(4));$d['features']=array_values(array_filter(array_map('trim',explode(',',(string)$r->features))));$d['event_types']=array_values(array_filter(array_map('trim',explode(',',(string)$r->event_types))));$d['gallery']=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)$r->gallery))));$d['is_verified']=$r->boolean('is_verified');$d['is_featured']=$r->boolean('is_featured');$d['published_at']=$r->boolean('published')?now():null;return $d;}
}
