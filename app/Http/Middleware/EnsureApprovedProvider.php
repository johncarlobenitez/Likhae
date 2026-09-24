<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedProvider
{
    public function handle(Request $request, Closure $next): Response
    {
        $provider = $request->user()?->logisticsProvider()->first();
        abort_if(! $provider || $provider->status !== 'approved', 403, 'An approved logistics provider account is required.');

        return $next($request);
    }
}
