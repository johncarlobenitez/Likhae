<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
            'services.google.redirect' => 'https://likhae.online/auth/google/callback',
        ]);
    }

    public function test_missing_google_configuration_returns_to_login(): void
    {
        config(['services.google.client_id' => null]);

        $this->get(route('google.redirect'))
            ->assertRedirectToRoute('login')
            ->assertSessionHasErrors('email');
    }

    public function test_active_marketplace_account_can_sign_in_and_link_google(): void
    {
        $account = User::factory()->create([
            'email' => 'buyer@example.com',
            'role' => 'buyer',
            'status' => 'active',
        ]);
        $this->mockGoogleUser('google-123', 'BUYER@example.com');

        $this->get(route('google.callback'))
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('buyer.home');

        $this->assertAuthenticatedAs($account);
        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'google_id' => 'google-123',
            'google_avatar_url' => 'https://example.test/avatar.jpg',
        ]);
    }

    public function test_new_google_buyer_must_complete_registration_and_approval(): void
    {
        Storage::fake('registrations');
        $this->mockGoogleUser('google-new', 'new@example.com');
        $this->get(route('google.callback'))
            ->assertRedirectToRoute('register', ['role' => 'buyer']);
        $this->assertGuest();

        $this->get(route('register', ['role' => 'buyer']))
            ->assertOk()
            ->assertSee('new@example.com');

        $this->post(route('register.store'), [
            'account_type' => 'seller',
            'first_name' => 'Google',
            'last_name' => 'Buyer',
            'sex' => 'Female',
            'birthday' => '1995-06-01',
            'contact_no' => '09171234567',
            'email' => 'changed@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'region' => '0400000000',
            'province' => '0403400000',
            'municipality' => '0403424000',
            'barangay' => '0403424001',
            'street' => 'Mabini Street',
            'postal_code' => '4000',
            'terms' => 'on',
            'valid_id' => UploadedFile::fake()->create('id.pdf', 10, 'application/pdf'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'role' => 'buyer',
            'status' => 'pending',
            'google_id' => 'google-new',
            'contact_number' => '09171234567',
            'street' => 'Mabini Street',
        ]);
        $this->assertGuest();

        $pending = User::factory()->create([
            'email' => 'pending@example.com',
            'role' => 'seller',
            'status' => 'pending',
        ]);
        $this->mockGoogleUser('google-pending', $pending->email);
        $this->get(route('google.callback'))
            ->assertRedirectToRoute('login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($pending->fresh()->google_id);
    }

    private function mockGoogleUser(string $id, string $email): void
    {
        $googleUser = (new GoogleUser)->map([
            'id' => $id,
            'email' => $email,
            'avatar' => 'https://example.test/avatar.jpg',
        ]);
        $googleUser->user = [
            'email_verified' => true,
            'given_name' => 'Google',
            'family_name' => 'Buyer',
        ];

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    }
}
