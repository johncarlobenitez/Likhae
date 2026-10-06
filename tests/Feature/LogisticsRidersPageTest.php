<?php

namespace Tests\Feature;

use App\Models\Logistics\LogisticsCenter;
use App\Models\Rider\RiderProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsRidersPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_riders_page_lists_all_account_statuses_for_its_center_across_pages(): void
    {
        $owner = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);
        $center = LogisticsCenter::create([
            'owner_user_id' => $owner->id,
            'code' => 'RIDERS-PAGE-CENTER',
            'business_name' => 'Riders Page Center',
            'status' => 'ACTIVE',
        ]);

        $first = null;
        $last = null;
        foreach (range(1, 16) as $number) {
            $status = match ($number % 3) {
                0 => 'PENDING',
                1 => 'ACTIVE',
                default => 'SUSPENDED',
            };
            $user = User::factory()->create(['account_type' => User::TYPE_RIDER, 'status' => $status]);
            RiderProfile::create([
                'user_id' => $user->id,
                'logistics_center_id' => $center->id,
                'vehicle_type' => 'motorcycle',
                'plate_number' => 'PAGE-PLATE-'.$number,
                'drivers_license_number' => 'PAGE-LICENSE-'.$number,
                'status' => $status,
            ]);
            $first ??= $user;
            $last = $user;
        }

        $otherOwner = User::factory()->create(['account_type' => User::TYPE_LOGISTICS]);
        $otherCenter = LogisticsCenter::create([
            'owner_user_id' => $otherOwner->id,
            'code' => 'OTHER-RIDERS-CENTER',
            'business_name' => 'Other Riders Center',
            'status' => 'ACTIVE',
        ]);
        $otherRider = User::factory()->create(['account_type' => User::TYPE_RIDER]);
        RiderProfile::create([
            'user_id' => $otherRider->id,
            'logistics_center_id' => $otherCenter->id,
            'vehicle_type' => 'motorcycle',
            'plate_number' => 'OTHER-PAGE-PLATE',
            'drivers_license_number' => 'OTHER-PAGE-LICENSE',
            'status' => 'ACTIVE',
        ]);

        $this->actingAs($owner)
            ->get(route('logistics.riders'))
            ->assertOk()
            ->assertSee($last->email)
            ->assertDontSee($first->email)
            ->assertDontSee($otherRider->email)
            ->assertSee('16');

        $this->get(route('logistics.riders', ['page' => 2]))
            ->assertOk()
            ->assertSee($first->email);
    }
}
