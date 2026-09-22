<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'google_id',
    'google_avatar_url',
    'email_verified_at',
    'password',
    'status',
    'first_name',
    'last_name',
    'middle_initial',
    'sex',
    'birthday',
    'contact_number',
    'valid_id_path',
    'profile_photo_path',
    'is_suspended',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements MustVerifyEmail
{
    public const MANAGED_ROLES = [
        'buyer',
        'seller',
        'logistics',
        'rider',
    ];

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'is_suspended' => 'boolean',
        ];
    }

    public function workspaceRoute(): string
    {
        if ($this->hasRole('admin')) {
            return 'admin.dashboard';
        }

        if ($this->hasRole('seller')) {
            return 'seller.dashboard';
        }

        if ($this->hasRole('logistics')) {
            return 'logistics.dashboard';
        }

        if ($this->hasRole('rider')) {
            return 'rider.dashboard';
        }

        return $this->hasRole('buyer')
            ? 'buyer.home'
            : 'home';
    }

    public function inactiveMessage(): string
    {
        if ($this->isSuspended()) {
            return 'Your account has been suspended. Please contact support for assistance.';
        }

        return match ($this->status) {
            'pending' =>
                'Your account is pending administrator approval.',

            'rejected' =>
                'Your registration has been rejected. Please contact support for assistance.',

            'suspended' =>
                'Your account has been suspended. Please contact support for assistance.',

            default =>
                'Your account is not active. Please contact support for assistance.',
        };
    }

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended
            || $this->status === 'suspended';
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }

    public function logisticsProvider(): HasOne
    {
        return $this->hasOne(LogisticsProvider::class);
    }

    public function rider(): HasOne
    {
        return $this->hasOne(Rider::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(
            Cart::class,
            'user_id'
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            'buyer_id'
        );
    }

    public function hasRole(string ...$roles): bool
    {
        $roles = collect($roles)
            ->filter()
            ->map(
                fn (string $role) =>
                    $role === 'courier'
                        ? 'rider'
                        : $role
            )
            ->unique()
            ->values()
            ->all();

        if ($roles === []) {
            return false;
        }

        return $this->relationLoaded('roles')
            ? collect($this->roles)
                ->pluck('name')
                ->intersect($roles)
                ->isNotEmpty()
            : $this->roles()
                ->whereIn('name', $roles)
                ->exists();
    }

    public function scopeRole(
        $query,
        string $role
    ) {
        $role = $role === 'courier'
            ? 'rider'
            : $role;

        return $query->whereHas(
            'roles',
            fn ($roles) =>
                $roles->where('name', $role)
        );
    }

    public function scopeAnyRole(
        $query,
        array $roles
    ) {
        $roles = collect($roles)
            ->map(
                fn ($role) =>
                    $role === 'courier'
                        ? 'rider'
                        : $role
            )
            ->unique();

        return $query->whereHas(
            'roles',
            fn ($query) =>
                $query->whereIn(
                    'name',
                    $roles
                )
        );
    }

    public function getPrimaryRoleAttribute(): string
    {
        foreach (
            [
                'admin',
                'seller',
                'logistics',
                'rider',
                'buyer',
            ] as $role
        ) {
            if ($this->hasRole($role)) {
                return $role;
            }
        }

        return 'user';
    }

    public function grant(string ...$roles): void
    {
        foreach ($roles as $role) {
            $normalized = $role === 'courier'
                ? 'rider'
                : $role;

            $record = Role::firstOrCreate([
                'name' => $normalized,
            ]);

            $this->roles()
                ->syncWithoutDetaching(
                    $record
                );
        }

        unset($this->relations['roles']);
    }
}