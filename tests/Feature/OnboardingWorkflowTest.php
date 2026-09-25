<?php

namespace Tests\Feature;

use App\Models\Buyer\Address;
use App\Models\Admin\AdminAuditLog;
use App\Models\Logistics\LogisticsProvider;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
        Storage::fake('public');
    }

    private function buyer(string $email = 'buyer@example.com'): User
    {
        return User::factory()->create(['email' => $email, 'role' => 'buyer', 'status' => 'active', 'email_verified_at' => now()]);
    }

    public function test_seller_application_approval_rejection_reapplication_and_suspension(): void
    {
        $buyer = $this->buyer();
        $address = Address::create(['user_id' => $buyer->id, 'label' => 'Pickup', 'recipient' => $buyer->name, 'phone' => '09170000000', 'line1' => '10 Main St', 'city' => 'Manila', 'province' => 'Metro Manila']);
        $payload = ['name' => 'Buyer Crafts', 'description' => 'Locally made goods', 'address_id' => $address->id, 'logo' => UploadedFile::fake()->image('logo.png', 200, 200), 'permit' => UploadedFile::fake()->create('permit.pdf', 100, 'application/pdf')];
        $this->actingAs($buyer)->post('/sell', $payload)->assertRedirectToRoute('seller.entry');
        $seller = Seller::where('user_id', $buyer->id)->sole();
        $this->assertSame('pending', $seller->status);
        $this->assertFalse($buyer->fresh()->hasRole('seller'));
        Storage::disk('local')->assertExists($seller->permit_path);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);
        $this->actingAs($buyer)->get(route('admin.sellers.permit', $seller))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.sellers.permit', $seller))->assertOk();
        $this->actingAs($admin)->post(route('admin.sellers.reject', $seller), ['reason' => 'Permit is unreadable.'])->assertRedirect();
        $this->actingAs($buyer)->get(route('seller.entry'))->assertOk()->assertSee('Permit is unreadable.');
        $this->post('/sell', [...$payload, 'logo' => UploadedFile::fake()->image('logo2.png'), 'permit' => UploadedFile::fake()->create('permit2.pdf', 100, 'application/pdf')])->assertRedirectToRoute('seller.entry');
        $seller->refresh();
        $this->assertSame('pending', $seller->status);
        $this->actingAs($admin)->post(route('admin.sellers.approve', $seller))->assertRedirect();
        $this->assertTrue($buyer->fresh()->hasRole('seller'));
        $this->actingAs($buyer)->get(route('seller.entry'))->assertRedirectToRoute('seller.dashboard');
        $this->actingAs($admin)->post(route('admin.sellers.reinstate', $seller))->assertUnprocessable();
        $this->actingAs($admin)->post(route('admin.sellers.suspend', $seller))->assertRedirect();
        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.sellers.reinstate', $seller))->assertRedirect();
        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertOk();
        $this->actingAs($admin)->post(route('admin.sellers.suspend', $seller))->assertRedirect();
        $this->assertSame(
            ['seller.rejected', 'seller.approved', 'seller.suspended', 'seller.reinstated', 'seller.suspended'],
            AdminAuditLog::where('target_id', $seller->id)->orderBy('id')->get()->map(fn ($log) => $log->action)->all(),
        );
    }

    public function test_approved_provider_creates_owned_rider_and_deactivation_blocks_workspace(): void
    {
        $owner = $this->buyer('owner@example.com');
        $this->actingAs($owner)->post('/partner', ['name' => 'Swift Express', 'contact_phone' => '09171112222', 'document' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf')])->assertRedirectToRoute('partner.status');
        $provider = LogisticsProvider::where('user_id', $owner->id)->sole();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);
        $this->actingAs($admin)->post(route('admin.couriers.approve', $provider))->assertRedirect();
        $this->assertTrue($owner->fresh()->hasRole('logistics'));

        $this->actingAs($admin)->post(route('admin.couriers.suspend', $provider))->assertRedirect();
        $this->actingAs($owner)->get(route('logistics.dashboard'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.couriers.reinstate', $provider))->assertRedirect();
        $this->actingAs($owner)->get(route('logistics.dashboard'))->assertOk();

        $response = $this->actingAs($owner)->post(route('logistics.riders.store'), ['name' => 'Rider One', 'email' => 'rider1@example.com', 'phone' => '09173334444', 'vehicle_type' => 'motorcycle', 'plate_no' => 'ABC-123']);
        $response->assertRedirect()->assertSessionHas('rider_password');
        $password = session('rider_password');
        $rider = Rider::with('user')->sole();
        $this->assertSame($provider->id, $rider->logistics_provider_id);
        $this->assertTrue(Hash::check($password, $rider->user->password));
        $this->actingAs($rider->user)->get(route('rider.dashboard'))->assertOk();
        $this->get(route('logistics.dashboard'))->assertForbidden();
        $this->actingAs($owner)->patch(route('logistics.riders.update', $rider), ['is_active' => 0])->assertRedirect();
        $this->actingAs($rider->user)->get(route('rider.dashboard'))->assertForbidden();
        $this->actingAs($owner)->patch(route('logistics.riders.activate', $rider), [])->assertRedirect();
        $this->actingAs($owner)->get(route('logistics.riders.edit', $rider))->assertOk();
        $this->actingAs($owner)->patch(route('logistics.riders.update', $rider), [
            'name' => 'Updated Rider', 'email' => 'rider-updated@example.com', 'phone' => '09175556666',
            'vehicle_type' => 'van', 'plate_no' => 'XYZ-789', 'is_active' => 1,
        ])->assertRedirect();
        $this->assertSame('Updated Rider', $rider->fresh()->user->name);
        $this->assertSame('van', $rider->fresh()->vehicle_type);
        $this->assertSame('XYZ-789', $rider->fresh()->plate_no);
    }

    public function test_provider_cannot_update_another_providers_rider(): void
    {
        $a = $this->buyer('a@example.com');
        $b = $this->buyer('b@example.com');
        foreach ([$a, $b] as $user) {
            $user->grant('logistics');
            LogisticsProvider::create(['user_id' => $user->id, 'name' => 'Provider '.$user->id, 'slug' => 'provider-'.$user->id, 'status' => 'approved']);
        }
        $riderUser = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $rider = Rider::create(['user_id' => $riderUser->id, 'logistics_provider_id' => $b->logisticsProvider->id, 'is_active' => true]);
        $this->actingAs($a)->patch(route('logistics.riders.update', $rider), ['is_active' => 0])->assertForbidden();
        $this->actingAs($a)->get(route('logistics.riders.show',$riderUser))->assertNotFound();
    }

    public function test_private_onboarding_documents_require_admin_and_all_courier_actions_are_audited(): void
    {
        $owner = $this->buyer('courier-audit@example.com');
        $this->actingAs($owner)->post('/partner', [
            'name' => 'Audit Express', 'contact_phone' => '09171112222',
            'document' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
        ])->assertRedirectToRoute('partner.status');
        $provider = LogisticsProvider::where('user_id', $owner->id)->sole();

        $this->actingAs($owner)->get(route('admin.couriers.document', $provider))->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);
        $this->actingAs($admin)->get(route('admin.couriers.document', $provider))->assertOk();
        $this->actingAs($admin)->post(route('admin.couriers.reject', $provider), ['reason' => 'Document needs correction.'])->assertRedirect();
        $this->assertSame('rejected', $provider->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'courier.rejected', 'target_id' => $provider->id]);

        $this->actingAs($owner)->post('/partner', [
            'name' => 'Audit Express Reapply', 'contact_phone' => '09171112222',
            'document' => UploadedFile::fake()->create('id2.pdf', 100, 'application/pdf'),
        ])->assertRedirectToRoute('partner.status');
        $provider->refresh();
        $this->actingAs($admin)->post(route('admin.couriers.approve', $provider))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.couriers.suspend', $provider))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.couriers.reinstate', $provider))->assertRedirect();
        $this->assertSame(
            ['courier.rejected', 'courier.approved', 'courier.suspended', 'courier.reinstated'],
            AdminAuditLog::where('target_id', $provider->id)->orderBy('id')->get()->map(fn ($log) => $log->action)->all(),
        );
    }
}
