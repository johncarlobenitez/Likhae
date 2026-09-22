<?php

use App\Http\Middleware\EnsureWorkspaceRole;
use App\Http\Middleware\EnsureActiveAccount;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
        |--------------------------------------------------------------------------
        | Trusted Proxies
        |--------------------------------------------------------------------------
        |
        | Railway and Cloudflare terminate TLS before forwarding the request
        | to Laravel, so Laravel must trust the forwarded proxy headers.
        |
        */

        $middleware->trustProxies(at: '*');

        /*
        |--------------------------------------------------------------------------
        | Web Middleware
        |--------------------------------------------------------------------------
        |
        | Keep the existing active-account middleware on browser/web routes.
        |
        | We are intentionally NOT automatically appending this to the API
        | middleware group yet. Protected mobile routes will be reviewed when
        | we wire mobile authentication.
        |
        */

        $middleware->web(
            append: [
                EnsureActiveAccount::class,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Guest Redirect
        |--------------------------------------------------------------------------
        |
        | Keep the current web behavior.
        |
        | API requests will return JSON because API routes / requests expecting
        | JSON are handled by the exception configuration below.
        |
        */

        $middleware->redirectGuestsTo(
            fn (Request $request) => route(
                $request->is(
                    'logistics/*',
                    'rider',
                    'rider/*'
                )
                    ? 'logistics.login'
                    : 'login'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Authenticated User Redirect
        |--------------------------------------------------------------------------
        */

        $middleware->redirectUsersTo(
            fn (Request $request) => route(
                $request->user()->workspaceRoute()
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        |
        | Preserve all existing LIKHAE role/workspace middleware.
        |
        */

        $middleware->alias([
            'role' => EnsureWorkspaceRole::class,

            'workspace.role' =>
                EnsureWorkspaceRole::class,

            'seller.approved' =>
                \App\Http\Middleware\EnsureApprovedSeller::class,

            'provider.approved' =>
                \App\Http\Middleware\EnsureApprovedProvider::class,

            'rider.active' =>
                \App\Http\Middleware\EnsureActiveRider::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
        |--------------------------------------------------------------------------
        | API JSON Exceptions
        |--------------------------------------------------------------------------
        |
        | API requests should return JSON errors instead of Laravel HTML error
        | pages. This is required for the Flutter mobile application.
        |
        */

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') ||
                $request->expectsJson(),
        );
    })
    ->create();