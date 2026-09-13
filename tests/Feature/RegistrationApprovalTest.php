<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('registrations');
    }

    public static function accountTypes(): array
    {
        return [
            'buyer' => ['buyer', 'buyer', '/login', 'buyer.home'],
            'seller' => ['seller', 'seller', '/login', 'seller.dashboard'],
            'logistics' => ['logistics', 'logistics', '/logistics/login', 'logistics.dashboard'],
            'rider' => ['rider', 'courier', '/logistics/login', 'rider.dashboard'],
        ];
    }

    private function application(string $type = 'buyer'): array
    {
        $data = [
            'account_type' => $type,
            'first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'Female',
            'birthday' => '1995-06-01', 'contact_no' => '09171234567',
            'email' => 'MARIA@example.com', 'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123',
            'region' => '0400000000', 'province' => '0403400000', 'municipality' => '0403401000', 'barangay' => '0403401001',
            'street' => 'Mabini Street', 'house_number' => '10', 'postal_code' => '4000', 'landmark' => 'Near town hall',
            'terms' => 'on', 'valid_id' => UploadedFile::fake()->create('id.pdf', 10, 'application/pdf'),
        ];
        if (in_array($type, ['seller', 'logistics'])) {
            $data += ['business_name' => 'Maria Trading', 'business_permit' => UploadedFile::fake()->create('permit.pdf', 10, 'application/pdf')];
        }
        if ($type === 'seller') {
            $data['line_of_business'] = 'Handicrafts';
        }
        if ($type === 'rider') {
            $data += [
                'vehicle_type' => 'motorcycle', 'plate_number' => 'ABC123',
                'or_cr' => UploadedFile::fake()->create('vehicle.pdf', 10, 'application/pdf'),
                'drivers_license' => UploadedFile::fake()->create('license.pdf', 10, 'application/pdf'),
            ];
        }

        return $data;
    }

    #[DataProvider('accountTypes')]
    public function test_registration_requires_approval_before_database_authentication(string $type, string $role, string $login, string $dashboard): void
    {
        $data = $this->application($type);
        $data['status'] = 'active';
        $data['reviewed_by'] = 999;
        $this->post('/register', $data)->assertSessionHasNoErrors()->assertRedirectToRoute('registration.pending', ['type' => ucfirst($type)]);
        $user = User::where('email', 'maria@example.com')->sole();
        $this->assertSame('pending', $user->status);
        $this->assertSame($role, $user->role);
        $this->assertNull($user->reviewed_by);
        $this->assertTrue(Hash::check('SecurePass123', $user->password));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'region' => '0400000000', 'postal_code' => '4000', 'landmark' => 'Near town hall', 'contact_number' => '09171234567']);
        Storage::disk('registrations')->assertExists($user->valid_id_path);
        $this->assertGuest();

        $credentials = ['email' => 'MARIA@EXAMPLE.COM', 'password' => 'SecurePass123'];
        $this->from($login)->post($login, $credentials)->assertSessionHasErrors('email');
        $this->assertGuest();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin)->get(route('admin.registrations', ['type' => $type === 'rider' ? 'riders' : ($type === 'logistics' ? $type : $type.'s')]))
            ->assertOk()->assertSee('maria@example.com');
        $this->post(route('admin.registrations.approve', $user))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active', 'reviewed_by' => $admin->id]);
        $this->assertNotNull($user->fresh()->reviewed_at);
        $this->post('/logout');

        $this->post($login, [...$credentials, 'remember' => '1'])->assertSessionHasNoErrors()->assertRedirectToRoute($dashboard);
        $this->assertAuthenticatedAs($user);
        $this->get(route($dashboard))->assertOk();
        $this->post('/logout')->assertRedirect($login);
        $this->assertGuest();
    }

    public function test_duplicate_email_validation_is_case_insensitive(): void
    {
        User::factory()->create(['email' => 'Maria@Example.com']);
        $this->post('/register', $this->application())->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
        $this->assertSame([], Storage::disk('registrations')->allFiles());
    }

    public function test_invalid_registration_does_not_create_an_account_or_store_uploads(): void
    {
        $this->post('/register', [...$this->application(), 'account_type' => 'admin', 'password_confirmation' => 'wrong', 'terms' => '0'])
            ->assertSessionHasErrors(['role', 'password', 'terms']);
        $this->assertDatabaseCount('users', 0);
        $this->assertSame([], Storage::disk('registrations')->allFiles());
    }

    public function test_documents_and_role_specific_fields_are_required(): void
    {
        $data = $this->application('rider');
        unset($data['valid_id'], $data['or_cr'], $data['drivers_license'], $data['plate_number'], $data['vehicle_type']);
        $this->post('/register', $data)->assertSessionHasErrors(['valid_id', 'or_cr', 'drivers_license', 'plate_number', 'vehicle_type']);
        $data = $this->application('seller');
        unset($data['business_name'], $data['business_permit'], $data['line_of_business']);
        $this->post('/register', $data)->assertSessionHasErrors(['business_name', 'business_permit', 'line_of_business']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_unsafe_and_oversized_documents_are_rejected(): void
    {
        $this->post('/register', [...$this->application(), 'valid_id' => UploadedFile::fake()->create('id.html', 10, 'text/html')])->assertSessionHasErrors('valid_id');
        $this->post('/register', [...$this->application(), 'valid_id' => UploadedFile::fake()->create('id.pdf', 5121, 'application/pdf')])->assertSessionHasErrors('valid_id');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_rejection_saves_a_reason_and_cannot_be_overwritten(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $applicant = User::factory()->create(['role' => 'buyer', 'status' => 'pending']);
        $this->actingAs($admin)->post(route('admin.registrations.reject', $applicant))->assertSessionHasErrors('rejection_reason');
        $this->post(route('admin.registrations.reject', $applicant), ['rejection_reason' => 'The ID is unreadable.'])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $applicant->id, 'status' => 'rejected', 'rejection_reason' => 'The ID is unreadable.', 'reviewed_by' => $admin->id]);
        $this->post(route('admin.registrations.approve', $applicant))->assertSessionHasErrors('application');
        $this->assertSame('rejected', $applicant->fresh()->status);
    }

    public function test_only_admins_can_review_applications_and_access_documents(): void
    {
        $applicant = User::factory()->create(['role' => 'buyer', 'valid_id_path' => 'registration/valid_id/id.pdf']);
        Storage::disk('registrations')->put($applicant->valid_id_path, 'private-document');
        $document = route('admin.registrations.document', [$applicant, 'valid_id']);
        $this->get($document)->assertRedirectToRoute('login');
        $this->post(route('admin.registrations.approve', $applicant))->assertRedirectToRoute('login');
        $buyer = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $this->actingAs($buyer)->get($document)->assertForbidden();
        $this->post(route('admin.registrations.approve', $applicant))->assertForbidden();
        $this->post(route('admin.registrations.reject', $applicant), ['rejection_reason' => 'No'])->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin)->get($document)->assertOk()->assertDownload('valid_id.pdf');
        $this->get(route('admin.registrations.document', [$applicant, 'password']))->assertNotFound();
        $this->post(route('admin.registrations.approve', $admin))->assertForbidden();
    }

    public function test_database_failure_removes_uploaded_documents(): void
    {
        User::creating(function () {
            throw new \RuntimeException('Simulated database failure');
        });
        try {
            $this->post('/register', $this->application())->assertServerError();
            $this->assertSame([], Storage::disk('registrations')->allFiles());
            $this->assertDatabaseCount('users', 0);
        } finally {
            User::flushEventListeners();
        }
    }
}
