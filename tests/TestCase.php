<?php
namespace Tests;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\TestCase as BaseTestCase;
abstract class TestCase extends BaseTestCase {
 protected $app;
 protected function setUp():void{
  parent::setUp();
  $this->app=require __DIR__.'/../bootstrap/app.php';
  $this->app->make(Kernel::class)->bootstrap();
  Artisan::call('migrate:fresh');
 }
 protected function tearDown():void{
  $this->app?->flush();
  restore_error_handler();
  restore_exception_handler();
  parent::tearDown();
 }
}
