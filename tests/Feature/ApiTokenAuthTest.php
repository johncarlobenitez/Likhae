<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_can_be_issued_used_and_revoked(): void
    {
        $user = User::factory()->create([
            'account_type' => User::TYPE_RIDER,
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $login = $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'Password1',
        ])->assertOk()->assertJsonPath('token_type', 'Bearer');

        $token = $login->json('access_token');
        $this->assertNotEmpty($token);
        $this->assertNotSame($token, $user->fresh()->api_token_hash);

        $this->withToken($token)
            ->getJson(route('api.v1.auth.me'))
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.account_type', User::TYPE_RIDER);

        $this->postJson(route('api.v1.auth.logout'))->assertOk();
        $this->getJson(route('api.v1.auth.me'))->assertUnauthorized();
    }
}
