<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminPanelRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $tab = (string) $request->input('_admin_tab', '');

        if ($response instanceof RedirectResponse && preg_match('/^(dashboard|media|reservations|calendar|visits|services|gallery|testimonials|contacts|settings|packages|menu|posts|faqs)$/', $tab)) {
            $url = $response->getTargetUrl();
            if (parse_url($url, PHP_URL_PATH) === parse_url(route('admin.dashboard'), PHP_URL_PATH) && !parse_url($url, PHP_URL_FRAGMENT)) {
                $response->setTargetUrl($url . '#admin-' . $tab);
            }
        }
        return $response;
    }
}
