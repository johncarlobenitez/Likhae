<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRider
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->rider()->where('is_active', true)->exists(), 403, 'This rider account is inactive.');

        return $next($request);
    }
}
