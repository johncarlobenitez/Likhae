<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $seller = $request->user()?->sellers()->latest()->first();
        abort_if(! $seller || $seller->status !== 'approved', 403, 'An approved seller account is required.');

        return $next($request);
    }
}
