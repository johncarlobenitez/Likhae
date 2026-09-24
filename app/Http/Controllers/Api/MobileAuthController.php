<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Authenticate a Buyer or Rider for the LIKHAE Flutter application.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
            ],
            'device_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $email = mb_strtolower(
            trim($credentials['email'])
        );

        $key = 'mobile-login:'.hash(
            'sha256',
            $email.'|'.$request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' =>
                    'Too many sign-in attempts. Try again in '.
                    RateLimiter::availableIn($key).
                    ' seconds.',
            ]);
        }

        RateLimiter::hit($key, 60);

        $user = User::query()
            ->where('email', $email)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Verify credentials first
        |--------------------------------------------------------------------------
        |
        | The existing web login only checks account status after Laravel has
        | successfully matched the credentials. Keep the mobile behavior the
        | same so an incorrect password does not expose account status.
        |
        */

        if (
            ! $user ||
            ! Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Existing LIKHAE account-state rules
        |--------------------------------------------------------------------------
        */

        if (
            $user->isSuspended() ||
            $user->status !== 'active'
        ) {
            throw ValidationException::withMessages([
                'email' => $user->inactiveMessage(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile role access
        |--------------------------------------------------------------------------
        |
        | LIKHAE mobile currently supports:
        |
        | - Buyer
        | - Rider
        |
        | Seller, Admin, and Logistics operations remain on the web system.
        |
        | A user may have multiple roles. Therefore, having another role does
        | not block mobile access as long as the same account also has Buyer
        | or Rider access.
        |
        */

        $user->load('roles');

        $mobileRoles = $this->mobileRoles($user);

        if ($mobileRoles === []) {
            throw ValidationException::withMessages([
                'email' =>
                    'This account does not have Buyer or Rider mobile access.',
            ]);
        }

        RateLimiter::clear($key);

        $deviceName = trim(
            (string) ($credentials['device_name'] ?? '')
        );

        if ($deviceName === '') {
            $deviceName = 'likhae-mobile';
        }

        /*
        |--------------------------------------------------------------------------
        | Sanctum token
        |--------------------------------------------------------------------------
        |
        | This creates a token for this specific mobile sign-in.
        | Existing tokens from other devices are intentionally retained.
        |
        */

        $token = $user
            ->createToken($deviceName)
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Signed in successfully.',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $this->userPayload(
                $user,
                $mobileRoles
            ),
        ]);
    }

    /**
     * Return the currently authenticated mobile user.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->load('roles');

        $mobileRoles = $this->mobileRoles($user);

        /*
        |--------------------------------------------------------------------------
        | Re-check account access
        |--------------------------------------------------------------------------
        |
        | A token may have been created while the account was active and the
        | account could later be suspended/deactivated. Do not return normal
        | authenticated access for an account that is no longer active.
        |
        */

        if (
            $user->isSuspended() ||
            $user->status !== 'active'
        ) {
            return response()->json([
                'success' => false,
                'message' => $user->inactiveMessage(),
            ], 403);
        }

        if ($mobileRoles === []) {
            return response()->json([
                'success' => false,
                'message' =>
                    'This account no longer has Buyer or Rider mobile access.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'user' => $this->userPayload(
                $user,
                $mobileRoles
            ),
        ]);
    }

    /**
     * Revoke only the token used for the current mobile session.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request
            ->user()
            ?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Signed out successfully.',
        ]);
    }

    /**
     * Determine which mobile workspaces are available to this user.
     *
     * @return array<int, string>
     */
    private function mobileRoles(User $user): array
    {
        $roles = [];

        if ($user->hasRole('buyer')) {
            $roles[] = 'buyer';
        }

        if ($user->hasRole('rider')) {
            $roles[] = 'rider';
        }

        return $roles;
    }

    /**
     * Build the user data returned to Flutter.
     *
     * Do not expose passwords, remember tokens, IDs/documents,
     * or unrelated private account information.
     *
     * @param  array<int, string>  $mobileRoles
     * @return array<string, mixed>
     */
    private function userPayload(
        User $user,
        array $mobileRoles
    ): array {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'middle_initial' => $user->middle_initial,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'contact_number' => $user->contact_number,
            'status' => $user->status,
            'email_verified' =>
                $user->email_verified_at !== null,
            'roles' => $user->roles
                ->pluck('name')
                ->values()
                ->all(),
            'mobile_roles' => $mobileRoles,
        ];
    }
}