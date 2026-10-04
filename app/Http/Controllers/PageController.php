<?php
namespace App\Http\Controllers;
use App\Models\{CeremonyService,Package,MenuItem,GalleryItem,Faq,Post,CalendarDate,Reservation};
use App\Support\JalaliDate;
class PageController extends Controller {
 public function about(){return view('pages.about');}
 public function services(){return view('pages.services',['services'=>CeremonyService::where('active',1)->orderBy('sort_order')->get()]);}
 public function service(CeremonyService $service){abort_unless($service->active,404);return view('pages.service',compact('service'));}
 public function packages(){return view('pages.packages',['packages'=>Package::where('active',1)->orderByDesc('featured')->orderBy('sort_order')->get()]);}
 public function menu(){return view('pages.menu',['items'=>MenuItem::where('active',1)->orderBy('category')->orderBy('sort_order')->get()->groupBy('category')]);}
 public function gallery(){return view('pages.gallery',['items'=>GalleryItem::where('active',1)->orderBy('sort_order')->get()]);}
 public function reservation(){
  $blocked=CalendarDate::whereDate('date','>=',today())->whereIn('status',['booked','unavailable'])->pluck('date')->map(fn($d)=>JalaliDate::format($d));
  $confirmed=Reservation::where('status','confirmed')->whereDate('event_date','>=',today())->pluck('event_date')->map(fn($d)=>JalaliDate::format($d));
  return view('pages.reservation',['packages'=>Package::where('active',1)->get(),'blockedDates'=>$blocked->merge($confirmed)->unique()->values()]);
 }
 public function faq(){return view('pages.faq',['faqs'=>Faq::where('active',1)->orderBy('sort_order')->get()]);}
 public function contact(){return view('pages.contact');}
 public function blog(){return view('blog.index',['posts'=>Post::whereNotNull('published_at')->latest('published_at')->paginate(9)]);}
 public function post(Post $post){abort_unless($post->published_at,404);return view('blog.show',compact('post'));}
}
