<?php
use App\Http\Controllers\HomeController; use App\Http\Controllers\VenueController; use App\Http\Controllers\BookingController; use App\Http\Controllers\AdminController; use Illuminate\Support\Facades\Route;
Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/talars',[VenueController::class,'index'])->name('venues.index');
Route::get('/talars/{venue:slug}',[VenueController::class,'show'])->name('venues.show');
Route::get('/compare',[VenueController::class,'compare'])->name('venues.compare');
Route::get('/reserve/{venue:slug}',[BookingController::class,'create'])->name('bookings.create');
Route::post('/reserve/{venue:slug}',[BookingController::class,'store'])->name('bookings.store');
Route::get('/admin/login',[AdminController::class,'login'])->name('admin.login');
Route::post('/admin/login',[AdminController::class,'authenticate'])->name('admin.authenticate');
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function(){
 Route::get('/',[AdminController::class,'dashboard'])->name('dashboard');
 Route::get('/venues',[AdminController::class,'venues'])->name('venues');
 Route::post('/venues',[AdminController::class,'storeVenue'])->name('venues.store');
 Route::put('/venues/{venue}',[AdminController::class,'updateVenue'])->name('venues.update');
 Route::delete('/venues/{venue}',[AdminController::class,'destroyVenue'])->name('venues.destroy');
 Route::post('/logout',[AdminController::class,'logout'])->name('logout');
});
