<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);
        $email = mb_strtolower(trim($credentials['email']));
        $request->merge(['email' => $email]);
        $key = 'login:'.hash('sha256', $email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts. Try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        RateLimiter::hit($key, 60);

        $authenticated = Auth::attemptWhen([
            fn ($query) => $query->whereRaw('LOWER(email) = ?', [$email]),
            'password' => $credentials['password'],
        ], function (User $user) use ($request): bool {
            if ($user->status !== 'active') {
                throw ValidationException::withMessages(['email' => $user->inactiveMessage()]);
            }

            $portal = $request->routeIs('logistics.login.store');
            $roles = $portal ? ['logistics', 'rider', 'courier'] : ['admin', 'buyer', 'seller'];

            if (! in_array($user->role, $roles, true)) {
                throw ValidationException::withMessages([
                    'email' => $portal
                        ? 'Use the Marketplace Login page for this account.'
                        : 'Use the Logistics Portal sign-in page for this account.',
                ]);
            }

            return true;
        }, $request->boolean('remember'));

        if (! $authenticated) {
            throw ValidationException::withMessages(['email' => 'Invalid email or password.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->forget('demo_user');

        return redirect()->route($request->user()->workspaceRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        $portal = in_array($request->user()?->role, ['logistics', 'rider', 'courier'], true);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($portal ? 'logistics.login' : 'login')
            ->with('status', 'You have been signed out.');
    }
}
