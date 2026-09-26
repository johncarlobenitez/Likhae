<?php

namespace Tests\Feature;

use App\Models\Auth\RegistrationApplication;
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
        $admin = User::factory()->create(['account_type' => User::TYPE_ADMIN]);
        $accountTypes = [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_LOGISTICS, User::TYPE_RIDER];

        foreach ($accountTypes as $index => $accountType) {
            $pending = User::factory()->create(['account_type' => $accountType, 'status' => User::STATUS_PENDING]);
            RegistrationApplication::create([
                'application_number' => 'APP-PENDING-'.$index,
                'user_id' => $pending->id,
                'status' => $index % 2 === 0 ? RegistrationApplication::STATUS_PENDING : RegistrationApplication::STATUS_UNDER_REVIEW,
                'submitted_at' => now(),
            ]);
            User::factory()->create(['account_type' => $accountType, 'status' => User::STATUS_ACTIVE]);
        }

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', [
                'pending' => count($accountTypes),
                'active' => count($accountTypes),
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
        $admin = User::factory()->create(['account_type' => User::TYPE_ADMIN]);
        foreach ([25, 48, 24, 23] as $index => $hours) {
            $user = User::factory()->create([
                'account_type' => [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_LOGISTICS, User::TYPE_RIDER][$index],
                'status' => User::STATUS_PENDING,
            ]);
            RegistrationApplication::create([
                'application_number' => 'APP-AGE-'.$index,
                'user_id' => $user->id,
                'status' => RegistrationApplication::STATUS_PENDING,
                'submitted_at' => now()->subHours($hours),
            ]);
        }
        User::factory()->create(['account_type' => User::TYPE_BUYER, 'status' => User::STATUS_ACTIVE]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', ['pending' => 4, 'active' => 1, 'pending_older_than_day' => 2])
            ->assertSee('2 older than 24 hours');
    }

    public function test_dashboard_shows_zero_when_no_public_accounts_exist(): void
    {
        $this->actingAs(User::factory()->create(['account_type' => User::TYPE_ADMIN]))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('accountStats', ['pending' => 0, 'active' => 0, 'pending_older_than_day' => 0])
            ->assertSee('0 older than 24 hours');
    }
}
