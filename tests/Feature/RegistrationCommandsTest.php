<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creation_uses_a_private_password_prompt_and_creates_an_active_account(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Administrator name', 'Administrator')
            ->expectsQuestion('Administrator email', 'ADMIN@example.com')
            ->expectsQuestion('Password (8-72 characters, uppercase, lowercase and a number)', 'SecurePass123')
            ->expectsQuestion('Confirm password', 'SecurePass123')
            ->assertSuccessful();
        $admin = User::where('email', 'admin@example.com')->sole();
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertSame('active', $admin->status);
        $this->assertTrue(Hash::check('SecurePass123', $admin->password));
    }

    public function test_admin_creation_does_not_promote_or_overwrite_existing_accounts(): void
    {
        $user = User::factory()->create(['email' => 'Existing@example.com', 'role' => 'buyer']);
        $this->artisan('app:create-admin')
            ->expectsQuestion('Administrator name', 'Administrator')
            ->expectsQuestion('Administrator email', 'existing@example.com')
            ->expectsQuestion('Password (8-72 characters, uppercase, lowercase and a number)', 'SecurePass123')
            ->expectsQuestion('Confirm password', 'SecurePass123')
            ->assertFailed();
        $this->assertTrue($user->fresh()->hasRole('buyer'));
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

}
