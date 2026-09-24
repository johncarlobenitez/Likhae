<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('registrations');
        config(['services.psgc.local_first' => true]);
    }

    private function application(array $overrides = []): array
    {
        return array_replace([
            'first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'female',
            'birthday' => '1995-06-01', 'contact_number' => '09171234567',
            'email' => 'MARIA@example.com', 'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'region' => 'Region IV-A (CALABARZON)', 'region_code' => '0400000000',
            'province' => 'Laguna', 'province_code' => '0403400000',
            'municipality' => 'City of San Pablo', 'municipality_code' => '0403424000',
            'barangay' => 'Bagong Bayan II-A', 'barangay_code' => '0403424001',
            'street' => 'Mabini Street', 'house_number' => '10', 'postal_code' => '4000',
            'landmark' => 'Near town hall', 'terms' => 'on',
            'valid_id' => UploadedFile::fake()->create('id.pdf', 10, 'application/pdf'),
        ], $overrides);
    }

    public function test_public_registration_creates_an_active_buyer_and_address(): void
    {
        $this->post('/register', $this->application(['account_type' => 'admin']))
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('verification.notice');

        $user = User::where('email', 'maria@example.com')->sole();
        $this->assertSame('active', $user->status);
        $this->assertTrue($user->hasRole('buyer'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertDatabaseHas('addresses', ['user_id' => $user->id, 'city' => 'City of San Pablo', 'is_default' => true]);
        Storage::disk('registrations')->assertExists($user->valid_id_path);
        $this->assertAuthenticatedAs($user);
    }

    public function test_duplicate_email_is_case_insensitive(): void
    {
        User::factory()->create(['email' => 'Maria@Example.com']);
        $this->post('/register', $this->application())->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_invalid_or_unsafe_registration_creates_nothing(): void
    {
        $this->post('/register', $this->application([
            'password_confirmation' => 'wrong',
            'valid_id' => UploadedFile::fake()->create('id.html', 10, 'text/html'),
        ]))->assertSessionHasErrors(['password', 'valid_id']);
        $this->assertDatabaseCount('users', 0);
        $this->assertSame([], Storage::disk('registrations')->allFiles());
    }

    public function test_database_failure_removes_private_upload(): void
    {
        User::creating(fn () => throw new \RuntimeException('Simulated database failure'));
        try {
            $this->post('/register', $this->application())->assertServerError();
            $this->assertSame([], Storage::disk('registrations')->allFiles());
        } finally {
            User::flushEventListeners();
        }
    }
}
