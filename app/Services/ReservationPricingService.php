<?php
namespace App\Services;
use App\Models\{Reservation,ReservationQuote};
use Illuminate\Support\Facades\DB;
class ReservationPricingService {
 public function __construct(private PriceCalculator $calculator){}
 public function recalculate(Reservation $reservation,array $selection,bool $newQuote=true):array{
  $pricing=$this->calculator->calculate($selection);
  DB::transaction(function()use($reservation,$pricing,$newQuote){
   $reservation->update(['base_price'=>$pricing['base_price'],'guest_total'=>$pricing['guest_total'],'menu_total'=>$pricing['menu_total'],'addon_total'=>$pricing['addon_total'],'date_adjustment_total'=>$pricing['date_adjustment_total'],'discount_total'=>$pricing['discount_total'],'final_price'=>$pricing['final_price'],'price_snapshot'=>$pricing]);
   $reservation->items()->delete();foreach($pricing['items'] as $item)$reservation->items()->create($item);
   if($newQuote){$latest=(int)$reservation->quotes()->max('version');$reservation->quotes()->whereIn('status',['issued','accepted'])->update(['status'=>'superseded']);ReservationQuote::create(['reservation_id'=>$reservation->id,'version'=>$latest+1,'subtotal'=>$pricing['subtotal'],'adjustment_total'=>$pricing['date_adjustment_total'],'discount_total'=>$pricing['discount_total'],'total'=>$pricing['final_price'],'status'=>'issued','snapshot'=>$pricing,'issued_at'=>now()]);}
  });
  return $pricing;
 }
}
