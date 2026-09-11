<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Please sign in first.']);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            $message = $user->inactiveMessage();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route(in_array($role, ['logistics', 'rider', 'courier'], true) ? 'logistics.login' : 'login')
                ->withErrors(['email' => $message]);
        }

        $actualRole = $user->role === 'courier' ? 'rider' : $user->role;
        $expectedRole = $role === 'courier' ? 'rider' : $role;
        abort_unless($actualRole === $expectedRole, 403);

        return $next($request);
    }
}
