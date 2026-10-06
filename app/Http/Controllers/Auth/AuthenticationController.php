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
        $this->rememberAccount($request, $user);

        return redirect()->route($user->workspaceRoute());
    }

    public function switchAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'account_id' => ['nullable', 'integer'],
        ]);

        $savedAccount = collect($request->session()->get('saved_accounts', []))
            ->filter(fn ($account): bool => is_array($account))
            ->first(fn (array $account): bool => (int) ($account['id'] ?? 0) === (int) ($data['account_id'] ?? 0));

        Auth::logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        if (! $savedAccount) {
            return redirect()->route('login')->with('status', 'Sign in with the account you want to use.');
        }

        $loginRoute = ($savedAccount['portal'] ?? 'marketplace') === 'logistics'
            ? 'logistics.login'
            : 'login';

        return redirect()->route($loginRoute, ['email' => $savedAccount['email']])
            ->with('status', 'Enter your password to switch accounts.');
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

    private function rememberAccount(Request $request, User $user): void
    {
        $portal = $user->isAccountType(User::TYPE_LOGISTICS, User::TYPE_RIDER)
            ? 'logistics'
            : 'marketplace';

        $currentAccount = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'account_type' => $user->account_type,
            'portal' => $portal,
        ];

        $accounts = collect($request->session()->get('saved_accounts', []))
            ->filter(fn ($account): bool => is_array($account) && filled($account['id'] ?? null))
            ->reject(fn (array $account): bool => (int) $account['id'] === (int) $user->id)
            ->prepend($currentAccount)
            ->take(5)
            ->values()
            ->all();

        $request->session()->put('saved_accounts', $accounts);
    }
}
