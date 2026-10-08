<?php
namespace App\Http\Middleware;
use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AdminMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $adminId = $request->session()->get('admin_user_id');
        if (!$adminId || !AdminUser::whereKey($adminId)->where('active', true)->exists()) {
            $request->session()->forget(['admin_user_id', 'admin_authenticated']);
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
