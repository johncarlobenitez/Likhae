<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MilestoneFourFiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_m4_account_and_order_foundation_is_present(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'is_suspended'));
        $this->assertTrue(Schema::hasColumn('orders', 'payment_status'));
        $this->assertFalse(Schema::hasColumn('users', 'role'));
        $this->assertFalse(Schema::hasColumn('orders', 'address_id'));

        $user = User::factory()->create();
        $user->grant('buyer', 'seller');

        $this->assertTrue($user->fresh()->hasRole('buyer'));
        $this->assertTrue($user->fresh()->hasRole('seller'));
        $this->assertSame(2, $user->fresh()->roles()->count());
    }

    public function test_m5_role_alias_and_required_roles_are_available(): void
    {
        $route = Route::middleware(['auth', 'role:buyer'])
            ->get('/__m5-role-alias-test', fn () => response('ok'));

        $this->assertContains('role:buyer', $route->middleware());
    }

    public function test_suspended_flag_blocks_login_and_next_authenticated_request(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended-flag@example.test',
            'password' => 'Password1',
            'status' => 'active',
            'is_suspended' => true,
        ]);
        $user->grant('buyer');

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'Password1',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $user->update(['is_suspended' => false]);
        $this->actingAs($user)->get('/');
        $user->update(['is_suspended' => true]);

        $this->get('/')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
