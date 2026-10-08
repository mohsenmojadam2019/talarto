<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller {
 public function loginForm(){return view('auth.login');}
 public function registerForm(){return view('auth.register');}
 public function register(Request $r){
  $d=$r->validate(['name'=>'required|string|min:2|max:120','mobile'=>['required','regex:/^09\d{9}$/','unique:users,mobile'],'password'=>'required|string|min:8|max:100|confirmed']);
  $user=User::create(['name'=>$d['name'],'mobile'=>$d['mobile'],'password'=>$d['password']]);Auth::login($user,true);$r->session()->regenerate();
  return redirect()->route('account.dashboard')->with('success','حساب شما ساخته شد.');
 }
 public function login(Request $r){
  $d=$r->validate(['mobile'=>['required','regex:/^09\d{9}$/'],'password'=>'required|string']);
  $user=User::where('mobile',$d['mobile'])->first();if(!$user||!Hash::check($d['password'],$user->password))return back()->withErrors(['mobile'=>'شماره موبایل یا رمز عبور صحیح نیست.'])->onlyInput('mobile');
  Auth::login($user,$r->boolean('remember'));$r->session()->regenerate();return redirect()->intended(route('account.dashboard'));
 }
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('home');}
}
