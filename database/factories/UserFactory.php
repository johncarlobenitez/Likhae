<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    public function configure(): static
    {
        return $this
            ->afterMaking(function (User $user): void {
                $role = $user->getAttribute('role');
                if ($role) {
                    $user->setRelation('_factory_role', $role);
                    $user->offsetUnset('role');
                }
                $name = trim((string) $user->getAttribute('name'));
                if ($name !== '') {
                    $parts = preg_split('/\s+/', $name) ?: [];
                    $user->first_name = array_shift($parts) ?: $user->first_name;
                    $user->last_name = implode(' ', $parts) ?: $user->last_name;
                    $user->offsetUnset('name');
                }
                $user->status = strtoupper((string) $user->status);
            })
            ->afterCreating(function (User $user): void {
                if ($user->relationLoaded('_factory_role')) {
                    $user->grant((string) $user->getRelation('_factory_role'));
                    $user->unsetRelation('_factory_role');
                }
            });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_initial' => fake()->optional()->randomLetter(),
            'last_name' => fake()->lastName(),
            'sex' => fake()->randomElement(['MALE', 'FEMALE']),
            'email' => fake()->unique()->safeEmail(),
            'contact_number' => '09'.fake()->numerify('#########'),
            'birthday' => fake()->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => 'ACTIVE',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
