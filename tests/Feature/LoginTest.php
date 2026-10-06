<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_buyer_can_sign_in_through_marketplace_login(): void
    {
        $buyer = User::factory()->create([
            'account_type' => User::TYPE_BUYER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->post(route('login.post'), [
            'email' => $buyer->email,
            'password' => 'Password1',
        ])->assertRedirect(route('buyer.home'));

        $this->assertAuthenticatedAs($buyer);
    }

    public function test_active_rider_can_sign_in_through_logistics_portal(): void
    {
        $rider = User::factory()->create([
            'account_type' => User::TYPE_RIDER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->post(route('logistics.login.store'), [
            'email' => $rider->email,
            'password' => 'Password1',
        ])->assertRedirect(route('rider.dashboard'));

        $this->assertAuthenticatedAs($rider);
    }

    public function test_invalid_password_is_rejected_without_authenticating(): void
    {
        $buyer = User::factory()->create([
            'account_type' => User::TYPE_BUYER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $buyer->email,
                'password' => 'WrongPassword1',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Invalid email or password.']);

        $this->assertGuest();
    }
}
