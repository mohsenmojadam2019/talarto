<?php
namespace App\Providers;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
 public function register():void{}
 public function boot():void {
  try {$settings=SiteSetting::query()->first();} catch (\Throwable) {$settings=null;}
  View::share('siteSettings',$settings);
 }
}
