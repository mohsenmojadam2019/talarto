<?php
namespace App\Http\Controllers;
use App\Models\{CeremonyService,Package,MenuItem,GalleryItem,Testimonial,Post,CalendarDate};
use App\Support\JalaliDate;
class HomeController extends Controller {
 public function index(){
  $blocked=CalendarDate::query()->whereDate('date','>=',today())->whereIn('status',['booked','unavailable'])->pluck('date')->map(fn($d)=>JalaliDate::format($d))->values();
  return view('home',[
   'services'=>CeremonyService::where('active',1)->orderBy('sort_order')->take(6)->get(),
   'packages'=>Package::where('active',1)->orderByDesc('featured')->orderBy('sort_order')->get(),
   'menuItems'=>MenuItem::where('active',1)->orderBy('category')->orderBy('sort_order')->get(),
   'gallery'=>GalleryItem::where('active',1)->orderBy('sort_order')->take(10)->get(),
   'testimonials'=>Testimonial::where('status','approved')->latest()->take(6)->get(),
   'posts'=>Post::whereNotNull('published_at')->latest('published_at')->take(3)->get(),
   'blockedDates'=>$blocked->unique()->values(),
  ]);
 }
}
