<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\EnsureTenantResolved;
use App\Http\Middleware\DefaultTenantFallback;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfNotAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        
        $middleware->alias([
            'tenant.resolve' => ResolveTenant::class,
            'tenant.ensure' => EnsureTenantResolved::class,
            'tenant.fallback' => DefaultTenantFallback::class,
            'guest' => RedirectIfAuthenticated::class,
            'auth' => RedirectIfNotAuthenticated::class,
        ]);
        
        // Temporarily disabled tenant middlewares for testing
        // $middleware->web(prepend: [
        //     DefaultTenantFallback::class,
        //     ResolveTenant::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
