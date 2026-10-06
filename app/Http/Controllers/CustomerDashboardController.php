<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class CustomerDashboardController extends Controller {
 public function index(Request $r){
  $reservations=$r->user()->reservations()->with(['package','latestQuote'])->latest()->get();
  $active=$reservations->first(fn($x)=>!in_array($x->status,['cancelled','done'],true));
  return view('account.dashboard',compact('reservations','active'));
 }
}
