<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user instanceof User && ! $user->isActive()) {
            $message = $user->inactiveMessage();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route(
                $request->is('logistics/*', 'rider', 'rider/*') ? 'logistics.login' : 'login'
            )->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
