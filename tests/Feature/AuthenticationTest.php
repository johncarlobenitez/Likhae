<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public static function inactiveAccounts(): array
    {
        return [
            ['pending', 'buyer', '/login', '/buyer/home'],
            ['rejected', 'buyer', '/login', '/buyer/home'],
            ['suspended', 'buyer', '/login', '/buyer/home'],
            ['unknown', 'buyer', '/login', '/buyer/home'],
            ['pending', 'logistics', '/logistics/login', '/logistics/parcels'],
            ['suspended', 'courier', '/logistics/login', '/rider/dashboard'],
            ['suspended', 'admin', '/login', '/admin/registrations'],
        ];
    }

    #[DataProvider('inactiveAccounts')]
    public function test_inactive_accounts_cannot_sign_in_or_keep_using_an_existing_session(string $status, string $role, string $login, string $workspace): void
    {
        $user = User::factory()->create(['role' => $role, 'status' => $status]);
        $this->post($login, ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->get($workspace)->assertRedirect($login);
        $this->assertGuest();
    }

    public function test_invalid_passwords_are_rate_limited_and_never_flashed_to_the_session(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email')->assertSessionMissing('_old_input.password');
        $this->assertStringContainsString('Too many', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_each_portal_accepts_only_its_own_roles(): void
    {
        $rider = User::factory()->create(['role' => 'courier', 'status' => 'active']);
        $this->post('/login', ['email' => $rider->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $this->post('/logistics/login', ['email' => $buyer->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_authenticates_with_database_credentials(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirectToRoute('admin.dashboard');
        $this->assertAuthenticatedAs($admin);
    }
}
