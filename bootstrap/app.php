<?php
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminPanelRedirectMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin'=>AdminMiddleware::class,'admin.panel'=>AdminPanelRedirectMiddleware::class]);
        $middleware->prependToPriorityList(\Illuminate\Routing\Middleware\SubstituteBindings::class,AdminMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();
