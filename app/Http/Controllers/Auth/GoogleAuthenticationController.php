<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthenticationController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in is not configured yet. Please use your email and password.',
            ]);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in is not configured yet. Please use your email and password.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in could not be completed. Please try again.',
            ]);
        }

        $email = mb_strtolower(trim((string) $googleUser->getEmail()));
        $verified = filter_var(data_get($googleUser->getRaw(), 'email_verified', false), FILTER_VALIDATE_BOOLEAN);

        if ($email === '' || ! $verified) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google did not provide a verified email address.',
            ]);
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $raw = $googleUser->getRaw();
            $request->session()->put('google_buyer_registration', [
                'id' => (string) $googleUser->getId(),
                'email' => $email,
                'first_name' => trim((string) ($raw['given_name'] ?? str($googleUser->getName())->beforeLast(' '))),
                'last_name' => trim((string) ($raw['family_name'] ?? str($googleUser->getName())->afterLast(' '))),
                'avatar' => $googleUser->getAvatar(),
            ]);

            return redirect()->route('register', ['role' => 'buyer']);
        }

        if (! $user->hasRole('buyer')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Continue with Google is available for buyer accounts only.',
            ]);
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')->withErrors(['email' => $user->inactiveMessage()]);
        }

        $googleId = (string) $googleUser->getId();
        if (filled($user->google_id) && ! hash_equals((string) $user->google_id, $googleId)) {
            return redirect()->route('login')->withErrors([
                'email' => 'This LIKHAE account is linked to a different Google account.',
            ]);
        }

        $user->forceFill([
            'google_id' => $googleId,
            'google_avatar_url' => $googleUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?: now(),
        ])->save();

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('demo_user');

        return redirect()->route($user->workspaceRoute());
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
