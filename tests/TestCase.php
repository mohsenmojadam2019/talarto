<?php
namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase {
 public function createApplication() {
  $app=require __DIR__.'/../bootstrap/app.php';
  $app->make(Kernel::class)->bootstrap();
  return $app;
 }
 protected function setUp():void {
  parent::setUp();
  Artisan::call('migrate:fresh');
 }
}
