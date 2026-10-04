<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class AdminGate{public function handle(Request $r,Closure $n):Response{if(!$r->session()->get('admin_authenticated'))return redirect()->route('admin.login');return $n($r);}}
