<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $isLogisticsPortal = $request->routeIs('logistics.login.store');

        $authenticated = Auth::attemptWhen(
            [
                'email' => $email,
                'password' => $credentials['password'],
            ],
            function (User $user) use ($isLogisticsPortal): bool {
                if (! $user->isActive()) {
                    throw ValidationException::withMessages(['email' => $user->inactiveMessage()]);
                }

                $allowed = $isLogisticsPortal
                    ? [User::TYPE_LOGISTICS, User::TYPE_RIDER]
                    : [User::TYPE_ADMIN, User::TYPE_BUYER, User::TYPE_SELLER];

                if (! $user->isAccountType(...$allowed)) {
                    throw ValidationException::withMessages([
                        'email' => $isLogisticsPortal
                            ? 'Use the Marketplace Login page for this account.'
                            : 'Use the Logistics & Rider Portal sign-in page for this account.',
                    ]);
                }

                return true;
            },
            $request->boolean('remember'),
        );

        if (! $authenticated) {
            throw ValidationException::withMessages(['email' => 'Invalid email or password.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->forget('demo_user');

        /** @var User $user */
        $user = $request->user();

        return redirect()->route($user->workspaceRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $portal = $user instanceof User
            && $user->isAccountType(User::TYPE_LOGISTICS, User::TYPE_RIDER);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route($portal ? 'logistics.login' : 'login')
            ->with('status', 'You have been signed out.');
    }
}
