<?php

namespace Tests\Feature;

use Tests\TestCase;

class WorkspaceRoleTest extends TestCase
{
    public function test_seller_can_access_seller_workspace_and_logistics_collaboration(): void
    {
        $this->withSession(['demo_user' => ['role' => 'seller']])
            ->get('/seller/dashboard')
            ->assertOk();

        $this->withSession(['demo_user' => ['role' => 'seller']])
            ->get('/seller/logistics')
            ->assertOk();
    }

    public function test_workspace_routes_reject_other_roles(): void
    {
        $this->withSession(['demo_user' => ['role' => 'seller']])
            ->get('/logistics/dashboard')
            ->assertForbidden();

        $this->withSession(['demo_user' => ['role' => 'logistics']])
            ->get('/courier/home')
            ->assertForbidden();
    }

    public function test_each_role_can_access_its_own_workspace(): void
    {
        $this->withSession(['demo_user' => ['role' => 'logistics']])
            ->get('/logistics/dashboard')
            ->assertOk();

        $this->withSession(['demo_user' => ['role' => 'courier']])
            ->get('/courier/home')
            ->assertOk();
    }

    public function test_users_without_a_role_are_sent_to_login(): void
    {
        $this->get('/seller/dashboard')
            ->assertRedirect('/login');
    }
}