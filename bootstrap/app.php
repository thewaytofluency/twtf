<?php

use App\Http\Middleware\EnsureDeployed;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'role' => EnsureUserHasRole::class,
        ]);

        // First in the group, before StartSession: with SESSION_DRIVER=database a brand-new
        // database has no `sessions` table yet, so the bootstrap must run before sessions are touched.
        $middleware->prependToGroup('web', EnsureDeployed::class);
        $middleware->appendToGroup('web', EnsureUserIsActive::class);

        // Behind a host's TLS proxy (Wasmer, Render...): trust it so URLs and redirects use https.
        if (env('TRUST_PROXIES')) {
            $middleware->trustProxies(at: '*');
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
