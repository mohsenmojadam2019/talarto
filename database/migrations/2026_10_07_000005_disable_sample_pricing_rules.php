<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
 public function up():void{
  DB::table('pricing_rules')->whereIn('title',['پنجشنبه','جمعه'])->update(['active'=>false,'updated_at'=>now()]);
 }
 public function down():void{
  DB::table('pricing_rules')->whereIn('title',['پنجشنبه','جمعه'])->update(['active'=>true,'updated_at'=>now()]);
 }
};
