<?php
namespace App\Http\Controllers;
use App\Models\Venue;
class HomeController extends Controller{public function index(){return view('home',['featured'=>Venue::whereNotNull('published_at')->where('is_featured',1)->take(6)->get(),'latest'=>Venue::whereNotNull('published_at')->latest('published_at')->take(8)->get()]);}}
