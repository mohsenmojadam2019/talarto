<?php
namespace App\Services;
use App\Models\{Addon,MenuItem,Package,PricingRule};
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
class PriceCalculator {
 public function calculate(array $data):array {
  $guests=max(1,(int)($data['guest_count']??0));
  $packageId=$data['package_id']??null;
  $package=$packageId?Package::query()->where('active',1)->find($packageId):null;
  if($packageId&&!$package) throw ValidationException::withMessages(['package_id'=>'پکیج انتخاب‌شده معتبر نیست.']);
  $billableGuests=$package?max($guests,(int)$package->min_guests):$guests;
  $items=[];$base=0;$guestTotal=0;$menuTotal=0;$addonTotal=0;
  if($package){
   $base=(int)$package->base_price;
   $guestTotal=(int)$package->per_guest_price*$billableGuests;
   $items[]=$this->line('package',$package->id,$package->title,'fixed',1,$base,$base,['slug'=>$package->slug]);
   if((int)$package->per_guest_price>0)$items[]=$this->line('package_guest',$package->id,'پذیرایی پایه '.$package->title,'per_guest',$billableGuests,(int)$package->per_guest_price,$guestTotal,['actual_guests'=>$guests,'min_guests'=>(int)$package->min_guests]);
  }
  $menuIds=array_values(array_unique(array_filter(array_map('intval',$data['menu_items']??[]))));
  if($menuIds){
   $menus=MenuItem::query()->where('active',1)->whereIn('id',$menuIds)->get()->keyBy('id');
   foreach($menuIds as $id){if(!$menus->has($id))throw ValidationException::withMessages(['menu_items'=>'یکی از آیتم‌های منو معتبر نیست.']);$m=$menus[$id];$total=(int)$m->price_per_guest*$guests;$menuTotal+=$total;$items[]=$this->line('menu',$m->id,$m->title,'per_guest',$guests,(int)$m->price_per_guest,$total,['category'=>$m->category]);}
  }
  $addonQty=$data['addons']??[];
  if(is_array($addonQty)&&$addonQty){
   $ids=array_values(array_unique(array_map('intval',array_keys($addonQty))));
   $addons=Addon::query()->where('active',1)->whereIn('id',$ids)->get()->keyBy('id');
   foreach($ids as $id){
    if(!$addons->has($id))throw ValidationException::withMessages(['addons'=>'یکی از خدمات جانبی معتبر نیست.']);
    $a=$addons[$id];$qty=(float)($addonQty[$id]??0);if($qty<=0)continue;
    $min=(float)$a->min_quantity;$max=$a->max_quantity!==null?(float)$a->max_quantity:null;
    if($qty<$min||($max!==null&&$qty>$max))throw ValidationException::withMessages(["addons.$id"=>'تعداد انتخاب‌شده برای '.$a->title.' معتبر نیست.']);
    [$effectiveQty,$total]=$this->addonTotal($a->pricing_type,(int)$a->unit_price,$qty,$guests);
    $addonTotal+=$total;$items[]=$this->line('addon',$a->id,$a->title,$a->pricing_type,$effectiveQty,(int)$a->unit_price,$total,['requested_quantity'=>$qty,'category'=>$a->category]);
   }
  }
  $subtotal=$base+$guestTotal+$menuTotal+$addonTotal;
  $date=$data['event_date'];$rules=$this->matchingRules($date,$packageId,$data['event_type']??null,$data['time_slot']??null);
  $adjustment=0;
  foreach($rules as $r){$raw=$r->rule_type==='percentage'?(int)round($subtotal*((int)$r->amount/100)):(int)$r->amount;$signed=$r->direction==='decrease'?-$raw:$raw;$adjustment+=$signed;$items[]=$this->line('pricing_rule',$r->id,$r->title,$r->rule_type,1,(int)$r->amount,$signed,['direction'=>$r->direction]);}
  $discount=max(0,(int)($data['discount_total']??0));
  $total=max(0,$subtotal+$adjustment-$discount);
  return ['base_price'=>$base,'guest_total'=>$guestTotal,'menu_total'=>$menuTotal,'addon_total'=>$addonTotal,'subtotal'=>$subtotal,'date_adjustment_total'=>$adjustment,'discount_total'=>$discount,'final_price'=>$total,'billable_guests'=>$billableGuests,'items'=>$items,'rules'=>$rules->map(fn($r)=>$r->only(['id','title','direction','rule_type','amount']))->values()->all()];
 }
 private function matchingRules(CarbonInterface $date,$packageId,?string $eventType,?string $slot){
  return PricingRule::query()->where('active',1)->orderBy('priority')->get()->filter(function($r)use($date,$packageId,$eventType,$slot){
   if($r->start_date&&$date->lt($r->start_date))return false;if($r->end_date&&$date->gt($r->end_date))return false;
   if($r->package_id&&((int)$r->package_id!==(int)$packageId))return false;if($r->event_type&&$r->event_type!==$eventType)return false;if($r->time_slot&&$r->time_slot!==$slot)return false;
   $weekdays=$r->weekdays?:[];if($weekdays&&!in_array($date->dayOfWeek,array_map('intval',$weekdays),true))return false;return true;
  })->values();
 }
 private function addonTotal(string $type,int $unit,float $qty,int $guests):array{
  return match($type){
   'per_guest'=>[$guests,(int)round($unit*$guests)],
   'per_table','per_hour','quantity'=>[$qty,(int)round($unit*$qty)],
   'included'=>[1,0],
   default=>[1,$unit],
  };
 }
 private function line(string $type,$id,string $title,string $pricingType,$qty,int $unit,int $total,array $meta=[]):array{return ['item_type'=>$type,'item_id'=>$id,'title_snapshot'=>$title,'pricing_type'=>$pricingType,'quantity'=>$qty,'unit_price'=>$unit,'total_price'=>$total,'metadata'=>$meta];}
}
