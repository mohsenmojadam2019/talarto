<?php
namespace Database\Seeders;
use App\Models\Venue; use Illuminate\Database\Seeder; use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder{public function run():void{$rows=[
['عمارت سپیدار','باغ تالار','تهران','گرمدره',120,700,1450000,'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1600&q=85'],
['تالار آریانا','تالار پذیرایی','تهران','سعادت‌آباد',100,450,1250000,'https://images.unsplash.com/photo-1507504031003-b417219a0fde?auto=format&fit=crop&w=1600&q=85'],
['خانه عقد ماهورا','سالن عقد','تهران','پاسداران',20,120,890000,'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1600&q=85'],
['باغ عمارت رویال','باغ تالار','البرز','شهریار',150,900,1650000,'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1600&q=85'],
['ایوان فیروزه','تالار پذیرایی','تهران','پیروزی',80,500,980000,'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=85'],
['عمارت رز سفید','باغ تالار','تهران','احمدآباد مستوفی',120,650,1350000,'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1600&q=85'],
['سالن جشن لیانا','سالن تولد','تهران','شهرک غرب',30,180,720000,'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=1600&q=85'],
['خانه عقد آینه','سالن عقد','تهران','ونک',20,100,1100000,'https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=1600&q=85'],
['باغ تالار ماهان','باغ تالار','تهران','جاجرود',100,800,1550000,'https://images.unsplash.com/photo-1478146896981-b80fe463b330?auto=format&fit=crop&w=1600&q=85'],
['تالار کلاسیک پارس','تالار پذیرایی','تهران','تهرانپارس',100,550,1050000,'https://images.unsplash.com/photo-1507504031003-b417219a0fde?auto=format&fit=crop&w=1600&q=85']];
foreach($rows as $i=>$r){Venue::create(['name'=>$r[0],'slug'=>Str::slug($r[0]).'-'.($i+1),'type'=>$r[1],'city'=>$r[2],'district'=>$r[3],'address'=>$r[2].'، '.$r[3],'capacity_min'=>$r[4],'capacity_max'=>$r[5],'price_from'=>$r[6],'price_to'=>$r[6]+650000,'short_description'=>'مجموعه مجهز برای برگزاری عروسی، عقد، نامزدی، تولد و مراسم خانوادگی.','description'=>'دارای فضای استاندارد پذیرایی، امکان انتخاب منو، دکور، نورپردازی و خدمات جانبی. جزئیات قیمت پیش از قرارداد استعلام شود.','cover_image'=>$r[7],'gallery'=>[$r[7],$r[7],$r[7]],'features'=>['پارکینگ اختصاصی','اتاق عقد','نورپردازی','سیستم صوتی','گل‌آرایی','فضای عکاسی'],'event_types'=>['عروسی','عقد','نامزدی','تولد'],'parking_capacity'=>120,'is_verified'=>true,'is_featured'=>$i<4,'discount_percent'=>$i%3===0?10:0,'meta_title'=>$r[0].' | قیمت، ظرفیت و رزرو','meta_description'=>'معرفی '.$r[0].'، ظرفیت، امکانات، قیمت و درخواست رزرو.','published_at'=>now()->subDays($i)]);}}}
