<?php
namespace App\Http\Controllers;
use App\Models\Venue; use Illuminate\Http\Request;
class VenueController extends Controller{
 public function index(Request $r){$q=Venue::whereNotNull('published_at')->when($r->type,fn($q,$v)=>$q->where('type',$v))->when($r->city,fn($q,$v)=>$q->where('city',$v))->when($r->district,fn($q,$v)=>$q->where('district','like',"%$v%"))->when($r->guests,fn($q,$v)=>$q->where('capacity_max','>=',(int)$v))->when($r->event,fn($q,$v)=>$q->whereJsonContains('event_types',$v))->when($r->max_price,fn($q,$v)=>$q->where('price_from','<=',(int)$v))->when($r->boolean('discount'),fn($q)=>$q->where('discount_percent','>',0))->orderByDesc('is_featured');return view('venues.index',['venues'=>$q->paginate(12)->withQueryString()]);}
 public function show(Venue $venue){abort_unless($venue->published_at,404);return view('venues.show',compact('venue'));}
 public function compare(Request $r){$ids=array_slice(array_filter(explode(',',(string)$r->ids)),0,4);return view('venues.compare',['venues'=>Venue::whereIn('id',$ids)->get()]);}
}
