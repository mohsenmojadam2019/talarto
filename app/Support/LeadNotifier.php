<?php
namespace App\Support;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class LeadNotifier {
 public static function send(string $subject,array $data):void {
  $to=(string)config('app.lead_email'); if($to==='')return;
  $body=collect($data)->map(fn($v,$k)=>$k.': '.(is_scalar($v)?$v:json_encode($v,JSON_UNESCAPED_UNICODE)))->implode("\n");
  try{Mail::raw($body,fn($m)=>$m->to($to)->subject($subject));}catch(\Throwable $e){Log::warning('Lead notification failed',['message'=>$e->getMessage()]);}
 }
}
