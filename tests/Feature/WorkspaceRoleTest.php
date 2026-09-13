<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_seller_can_access_seller_workspace_and_logistics_collaboration(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'seller', 'status' => 'active']))
            ->get('/seller/dashboard')
            ->assertOk();

        $this->actingAs(User::factory()->create(['role' => 'seller', 'status' => 'active']))
            ->get('/seller/logistics')
            ->assertOk();
    }

    public function test_workspace_routes_reject_other_roles(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'seller', 'status' => 'active']))
            ->get('/logistics/dashboard')
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'logistics', 'status' => 'active']))
            ->get('/rider/dashboard')
            ->assertForbidden();
    }

    public function test_each_role_can_access_its_own_workspace(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'logistics', 'status' => 'active']))
            ->get('/logistics/dashboard')
            ->assertOk();

        $this->actingAs(User::factory()->create(['role' => 'courier', 'status' => 'active']))
            ->get('/rider/dashboard')
            ->assertOk();
    }

    public function test_users_without_a_role_are_sent_to_login(): void
    {
        $this->get('/seller/dashboard')
            ->assertRedirect('/login');
    }

    public function test_demo_sessions_do_not_authenticate_users(): void
    {
        $this->withSession(['demo_user' => ['role' => 'admin']])->get('/admin/registrations')->assertRedirect('/login');
        $this->get('/logistics/parcels')->assertRedirect('/logistics/login');
    }
}
