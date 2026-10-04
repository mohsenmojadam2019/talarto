<?php
namespace App\Http\Controllers;
use App\Models\{CeremonyService,Post};
class SeoController extends Controller {
 public function sitemap(){
  $urls=[route('home'),route('about'),route('services.index'),route('packages'),route('menu'),route('gallery'),route('calendar'),route('calculator'),route('reservation'),route('faq'),route('contact'),route('terms'),route('privacy'),route('blog.index')];
  foreach(CeremonyService::where('active',1)->get() as $s)$urls[]=route('services.show',$s);
  foreach(Post::whereNotNull('published_at')->get() as $p)$urls[]=route('blog.show',$p);
  $xml=view('seo.sitemap',compact('urls'))->render(); return response($xml,200,['Content-Type'=>'application/xml; charset=UTF-8']);
 }
 public function robots(){return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".route('sitemap')."\n",200,['Content-Type'=>'text/plain']);}
}