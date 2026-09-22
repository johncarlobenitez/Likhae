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
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway and Cloudflare terminate TLS before forwarding to Laravel.
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [EnsureActiveAccount::class]);
        $middleware->redirectGuestsTo(fn (Request $request) => route(
            $request->is('logistics/*', 'rider', 'rider/*') ? 'logistics.login' : 'login'
        ));
        $middleware->redirectUsersTo(fn (Request $request) => route($request->user()->workspaceRoute()));
        $middleware->alias([
            'role' => EnsureWorkspaceRole::class,
            'workspace.role' => EnsureWorkspaceRole::class,
            'seller.approved' => \App\Http\Middleware\EnsureApprovedSeller::class,
            'provider.approved' => \App\Http\Middleware\EnsureApprovedProvider::class,
            'rider.active' => \App\Http\Middleware\EnsureActiveRider::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
