<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $this->assertSame('admin', $admin->role);
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
        $this->assertSame('buyer', $user->fresh()->role);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_old_public_documents_are_moved_without_changing_database_references(): void
    {
        Storage::fake('public');
        Storage::fake('registrations');
        $path = 'registration/valid-ids/existing.pdf';
        User::factory()->create(['valid_id_path' => $path]);
        Storage::disk('public')->put($path, 'original-content');
        $this->artisan('app:secure-registration-documents')->assertSuccessful();
        Storage::disk('public')->assertMissing($path);
        $this->assertSame('original-content', Storage::disk('registrations')->get($path));
        $this->assertDatabaseHas('users', ['valid_id_path' => $path]);
        $this->artisan('app:secure-registration-documents')->assertSuccessful();
    }

    public function test_conflicting_private_copies_preserve_the_original_document(): void
    {
        Storage::fake('public');
        Storage::fake('registrations');
        $path = 'registration/valid-ids/existing.pdf';
        User::factory()->create(['valid_id_path' => $path]);
        Storage::disk('public')->put($path, 'original-content');
        Storage::disk('registrations')->put($path, 'different-content');
        $this->artisan('app:secure-registration-documents')->assertFailed();
        $this->assertSame('original-content', Storage::disk('public')->get($path));
        $this->assertSame('different-content', Storage::disk('registrations')->get($path));
    }
}
