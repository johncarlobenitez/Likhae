<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dashboard_counts_only_public_accounts_in_the_matching_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        foreach (User::PUBLIC_ROLES as $role) {
            User::factory()->create(['role' => $role, 'status' => 'pending']);
            User::factory()->create(['role' => $role, 'status' => 'active']);
            User::factory()->create(['role' => $role, 'status' => 'rejected']);
            User::factory()->create(['role' => $role, 'status' => 'suspended']);
        }

        User::factory()->create(['role' => 'admin', 'status' => 'pending']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', [
                'pending' => count(User::PUBLIC_ROLES),
                'active' => count(User::PUBLIC_ROLES),
                'pending_older_than_day' => 0,
            ])
            ->assertSee('Awaiting administrator review')
            ->assertSee('Approved marketplace accounts')
            ->assertDontSee('12,480')
            ->assertDontSee('+4.2%');
    }

    public function test_pending_queue_counts_only_applications_older_than_twenty_four_hours(): void
    {
        $this->freezeTime();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        User::factory()->create(['role' => 'buyer', 'status' => 'pending', 'created_at' => now()->subHours(25)]);
        User::factory()->create(['role' => 'courier', 'status' => 'pending', 'created_at' => now()->subDays(2)]);
        User::factory()->create(['role' => 'seller', 'status' => 'pending', 'created_at' => now()->subDay()]);
        User::factory()->create(['role' => 'logistics', 'status' => 'pending', 'created_at' => now()->subHours(23)]);
        User::factory()->create(['role' => 'buyer', 'status' => 'active', 'created_at' => now()->subDays(2)]);
        User::factory()->create(['role' => 'buyer', 'status' => 'rejected', 'created_at' => now()->subDays(2)]);
        User::factory()->create(['role' => 'admin', 'status' => 'pending', 'created_at' => now()->subDays(2)]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', ['pending' => 4, 'active' => 1, 'pending_older_than_day' => 2])
            ->assertSee('2 older than 24 hours');
    }

    public function test_dashboard_shows_zero_when_no_public_accounts_exist(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'active']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', ['pending' => 0, 'active' => 0, 'pending_older_than_day' => 0])
            ->assertSee('0 older than 24 hours');
    }
}
