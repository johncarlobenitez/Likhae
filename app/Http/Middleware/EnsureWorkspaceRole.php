<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {

        $user = $request
            ->session()
            ->get('demo_user');


        /*
        |--------------------------------------------------------------------------
        | Not Logged In
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please sign in first.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Wrong Workspace
        |--------------------------------------------------------------------------
        */

        if (($user['role'] ?? null) !== $role) {

            return $this->redirectToWorkspace(
                $user['role'] ?? null
            );
        }


        return $next($request);
    }


    /**
     * Redirect user to their correct workspace.
     */
    private function redirectToWorkspace(
        ?string $role
    ): Response {

        return match ($role) {

            'admin' => redirect()
                ->route('admin.dashboard'),

            'buyer' => redirect()
                ->route('buyer.home'),

            'seller' => redirect()
                ->route('seller.dashboard'),

            'logistics' => redirect()
                ->route('logistics.dashboard'),

            'rider' => redirect()
                ->route('rider.dashboard'),

            default => redirect()
                ->route('login'),
        };
    }
}