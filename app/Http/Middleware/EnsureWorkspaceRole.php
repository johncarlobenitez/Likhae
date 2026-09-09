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

        if ($user->role !== $role) {
            return $this->redirectToWorkspace($user->role);
        }

        return $next($request);
    }

    private function redirectToWorkspace(?string $role): Response
    {
        return match ($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'buyer'     => redirect()->route('buyer.home'),
            'seller'    => redirect()->route('seller.dashboard'),
            'logistics' => redirect()->route('logistics.dashboard'),
            'rider', 'courier' => redirect()->route('rider.dashboard'),
            default     => redirect()->route('login'),
        };
    }
}
