<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function administrator(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public static function publicRoles(): array
    {
        return array_map(fn (string $role) => [$role], User::PUBLIC_ROLES);
    }

    public function test_guests_cannot_read_or_change_managed_accounts(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $this->get(route('admin.users'))->assertRedirectToRoute('login');
        $this->get(route('admin.users.show', $user))->assertRedirectToRoute('login');
        $this->post(route('admin.users.suspend', $user), ['reason' => 'Unauthorized'])
            ->assertRedirectToRoute('login');
        $this->post(route('admin.users.reactivate', $user), ['reason' => 'Unauthorized'])
            ->assertRedirectToRoute('login');

        $this->assertSame('active', $user->fresh()->status);
        $this->assertDatabaseCount('user_status_changes', 0);
    }

    #[DataProvider('publicRoles')]
    public function test_public_roles_cannot_read_or_change_other_accounts(string $role): void
    {
        $actor = User::factory()->create(['role' => $role, 'status' => 'active']);
        $active = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $suspended = User::factory()->create(['role' => 'seller', 'status' => 'suspended']);

        $this->actingAs($actor)->get(route('admin.users'))->assertForbidden();
        $this->get(route('admin.users.show', $active))->assertForbidden();
        $this->post(route('admin.users.suspend', $active), ['reason' => 'Unauthorized'])->assertForbidden();
        $this->post(route('admin.users.reactivate', $suspended), ['reason' => 'Unauthorized'])->assertForbidden();

        $this->assertSame('active', $active->fresh()->status);
        $this->assertSame('suspended', $suspended->fresh()->status);
        $this->assertDatabaseCount('user_status_changes', 0);
    }

    public function test_directory_lists_real_accounts_and_filters_each_role_including_rider_aliases(): void
    {
        $admin = $this->administrator();
        $accounts = collect(['buyer', 'seller', 'logistics', 'courier', 'rider', 'admin'])
            ->mapWithKeys(fn (string $role) => [$role => User::factory()->create([
                'name' => 'Directory '.$role,
                'email' => 'directory-'.$role.'@example.test',
                'role' => $role,
                'status' => 'active',
            ])]);

        $response = $this->actingAs($admin)->get(route('admin.users'))->assertOk();
        foreach ($accounts as $account) {
            $response->assertSee($account->email);
            $response->assertSee(route('admin.users.show', $account));
        }

        foreach (['buyers' => ['buyer'], 'sellers' => ['seller'], 'logistics' => ['logistics'], 'riders' => ['courier', 'rider'], 'admins' => ['admin']] as $filter => $roles) {
            $response = $this->get(route('admin.users', ['role' => $filter]))->assertOk();
            foreach ($accounts as $role => $account) {
                if (in_array($role, $roles, true)) {
                    $response->assertSee($account->email);
                } else {
                    $response->assertDontSee($account->email);
                }
            }
        }
    }

    public function test_directory_filters_every_account_status(): void
    {
        $admin = $this->administrator();
        $accounts = collect(['pending', 'active', 'rejected', 'suspended'])
            ->mapWithKeys(fn (string $status) => [$status => User::factory()->create([
                'role' => 'buyer', 'status' => $status, 'email' => 'status-'.$status.'@example.test',
            ])]);
        $this->actingAs($admin);

        foreach ($accounts as $status => $expected) {
            $response = $this->get(route('admin.users', ['status' => $status]))->assertOk();
            $response->assertSee($expected->email);
            foreach ($accounts->except($status) as $excluded) {
                $response->assertDontSee($excluded->email);
            }
        }
    }

    public function test_search_matches_saved_name_email_and_both_account_id_formats(): void
    {
        $admin = $this->administrator();
        $target = User::factory()->create([
            'name' => 'Mariner Santos', 'email' => 'harbor.person@example.test', 'role' => 'seller', 'status' => 'active',
        ]);
        $other = User::factory()->create([
            'name' => 'Unrelated Person', 'email' => 'unrelated.person@example.test', 'role' => 'buyer', 'status' => 'active',
        ]);
        $this->actingAs($admin);

        foreach (['Mariner', 'harbor.person', (string) $target->id, 'USR-'.$target->id] as $query) {
            $this->get(route('admin.users', ['q' => $query]))->assertOk()
                ->assertSee($target->email)->assertDontSee($other->email);
        }

        $this->get(route('admin.users', ['q' => 'NobodyHasThisName']))->assertOk()
            ->assertDontSee($target->email)->assertDontSee($other->email);
    }

    public function test_search_does_not_bypass_combined_role_and_status_filters(): void
    {
        $admin = $this->administrator();
        $target = User::factory()->create([
            'name' => 'Harbor Target', 'email' => 'target@example.test', 'role' => 'buyer', 'status' => 'suspended',
        ]);
        $otherStatus = User::factory()->create([
            'name' => 'Harbor Active', 'email' => 'active@example.test', 'role' => 'buyer', 'status' => 'active',
        ]);
        $otherRole = User::factory()->create([
            'name' => 'Another Person', 'email' => 'harbor.seller@example.test', 'role' => 'seller', 'status' => 'suspended',
        ]);

        $this->actingAs($admin)->get(route('admin.users', ['q' => 'Harbor', 'role' => 'buyers', 'status' => 'suspended']))
            ->assertOk()->assertSee($target->email)->assertDontSee($otherStatus->email)->assertDontSee($otherRole->email);
    }

    public function test_directory_paginates_filtered_database_accounts_without_losing_filters(): void
    {
        $admin = $this->administrator();
        $accounts = User::factory()->count(23)->sequence(fn ($sequence) => [
            'name' => 'Pagination Person '.$sequence->index,
            'email' => 'pagination-'.$sequence->index.'@example.test',
            'role' => 'buyer', 'status' => 'active',
        ])->create();
        $filters = ['role' => 'buyers', 'status' => 'active', 'q' => 'Pagination'];
        $first = $this->actingAs($admin)->get(route('admin.users', $filters))->assertOk();
        $second = $this->get(route('admin.users', [...$filters, 'page' => 2]))->assertOk();

        $firstEmails = $accounts->filter(fn (User $user) => str_contains($first->getContent(), $user->email))->pluck('email');
        $secondEmails = $accounts->filter(fn (User $user) => str_contains($second->getContent(), $user->email))->pluck('email');
        $this->assertCount(20, $firstEmails);
        $this->assertCount(3, $secondEmails);
        $this->assertCount(23, $firstEmails->merge($secondEmails)->unique());
        $first->assertSee('role=buyers')->assertSee('status=active')->assertSee('q=Pagination')->assertSee('page=2');
    }

    public function test_invalid_directory_filters_and_oversized_search_are_validated(): void
    {
        $this->actingAs($this->administrator());

        foreach ([['role' => 'owner'], ['status' => 'deleted'], ['q' => str_repeat('x', 101)], ['q' => ['invalid']]] as $query) {
            $this->from(route('admin.users'))->get(route('admin.users', $query))
                ->assertSessionHasErrors(array_key_first($query));
        }
    }

    public function test_account_profile_shows_saved_details_and_escapes_user_content_and_audit_reasons(): void
    {
        $admin = $this->administrator();
        $user = User::factory()->create([
            'name' => '<script>alert("profile")</script>', 'email' => 'profile@example.test',
            'role' => 'seller', 'status' => 'active', 'contact_number' => '09171234567',
            'business_name' => '<img src=x onerror=alert(1)>', 'store_name' => 'Handmade Harbor',
            'street' => 'Mabini Extension', 'postal_code' => '4000',
            'valid_id_path' => 'private/uploads/secret-id.pdf',
        ]);
        $reason = '<script>alert("audit")</script>';
        $this->actingAs($admin)->post(route('admin.users.suspend', $user), ['reason' => $reason])
            ->assertSessionHasNoErrors();

        $this->get(route('admin.users.show', $user))->assertOk()
            ->assertSee($user->name)->assertDontSee($user->name, false)
            ->assertSee($user->email)->assertSee($user->contact_number)
            ->assertSee($user->business_name)->assertDontSee($user->business_name, false)
            ->assertSee($user->store_name)->assertSee($user->street)
            ->assertSee($reason)->assertDontSee($reason, false)
            ->assertDontSee($user->password, false)->assertDontSee($user->remember_token, false)
            ->assertDontSee($user->valid_id_path, false);
    }

    #[DataProvider('publicRoles')]
    public function test_status_changes_are_audited_and_preserve_registration_review(string $role): void
    {
        $reviewer = $this->administrator();
        $admin = $this->administrator();
        $user = User::factory()->create([
            'role' => $role, 'status' => 'active', 'reviewed_by' => $reviewer->id,
            'reviewed_at' => '2026-08-20 09:00:00', 'rejection_reason' => 'Historical registration note',
        ]);
        $rememberToken = $user->remember_token;
        $approval = $user->only(['reviewed_by', 'reviewed_at', 'rejection_reason']);

        $this->actingAs($admin)->post(route('admin.users.suspend', $user), ['reason' => 'Repeated delivery issues'])
            ->assertSessionHasNoErrors()->assertRedirectToRoute('admin.users.show', $user);
        $this->assertSame('suspended', $user->fresh()->status);
        $this->assertNotSame($rememberToken, $user->fresh()->remember_token);
        $this->assertDatabaseHas('user_status_changes', [
            'user_id' => $user->id, 'changed_by' => $admin->id, 'previous_status' => 'active',
            'status' => 'suspended', 'reason' => 'Repeated delivery issues',
        ]);

        $this->post(route('admin.users.reactivate', $user), ['reason' => 'Issue resolved with account holder'])
            ->assertSessionHasNoErrors()->assertRedirectToRoute('admin.users.show', $user);
        $this->assertSame('active', $user->fresh()->status);
        $this->assertEquals($approval, $user->fresh()->only(['reviewed_by', 'reviewed_at', 'rejection_reason']));
        $this->assertDatabaseHas('user_status_changes', [
            'user_id' => $user->id, 'changed_by' => $admin->id, 'previous_status' => 'suspended',
            'status' => 'active', 'reason' => 'Issue resolved with account holder',
        ]);
        $this->assertDatabaseCount('user_status_changes', 2);
        $this->get(route('admin.users.show', $user))->assertOk()
            ->assertSee('Repeated delivery issues')->assertSee('Issue resolved with account holder');
    }

    public function test_repeated_status_actions_do_not_overwrite_or_duplicate_the_audit(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $this->actingAs($this->administrator());
        $this->post(route('admin.users.suspend', $user), ['reason' => 'Original decision'])->assertSessionHasNoErrors();
        $original = DB::table('user_status_changes')->first();

        $this->post(route('admin.users.suspend', $user), ['reason' => 'Duplicate decision'])->assertSessionHasErrors('status');
        $this->assertSame('suspended', $user->fresh()->status);
        $this->assertDatabaseCount('user_status_changes', 1);
        $this->assertEquals($original, DB::table('user_status_changes')->first());

        $this->post(route('admin.users.reactivate', $user), ['reason' => 'Resolved'])->assertSessionHasNoErrors();
        $this->post(route('admin.users.reactivate', $user), ['reason' => 'Duplicate resolution'])->assertSessionHasErrors('status');
        $this->assertSame('active', $user->fresh()->status);
        $this->assertDatabaseCount('user_status_changes', 2);
        $this->assertEquals($original, DB::table('user_status_changes')->where('id', $original->id)->first());
    }

    public function test_pending_and_rejected_accounts_cannot_bypass_registration_approval(): void
    {
        $this->actingAs($this->administrator());

        foreach (['pending', 'rejected'] as $status) {
            $user = User::factory()->create(['role' => 'seller', 'status' => $status]);
            foreach (['suspend', 'reactivate'] as $action) {
                $this->post(route('admin.users.'.$action, $user), ['reason' => 'Attempted bypass'])
                    ->assertSessionHasErrors('status');
                $this->assertSame($status, $user->fresh()->status);
                $this->assertNull($user->fresh()->reviewed_at);
            }
        }

        $this->assertDatabaseCount('user_status_changes', 0);
    }

    public function test_administrators_cannot_change_their_own_or_other_administrators_status(): void
    {
        $admin = $this->administrator();
        $other = $this->administrator();
        $suspendedAdmin = User::factory()->create(['role' => 'admin', 'status' => 'suspended']);
        $this->actingAs($admin);

        foreach ([$admin, $other, $suspendedAdmin] as $target) {
            foreach (['suspend', 'reactivate'] as $action) {
                $this->post(route('admin.users.'.$action, $target), ['reason' => 'Cannot target administrators'])
                    ->assertForbidden();
            }
            $this->assertSame($target->status, $target->fresh()->status);
        }

        $this->assertDatabaseCount('user_status_changes', 0);
    }

    public function test_both_status_actions_require_a_nonempty_reason_of_at_most_2000_characters(): void
    {
        $this->actingAs($this->administrator());

        foreach (['suspend' => 'active', 'reactivate' => 'suspended'] as $action => $status) {
            $user = User::factory()->create(['role' => 'buyer', 'status' => $status]);
            foreach ([[], ['reason' => ''], ['reason' => '   '], ['reason' => ['invalid']], ['reason' => str_repeat('x', 2001)]] as $input) {
                $this->post(route('admin.users.'.$action, $user), $input)->assertSessionHasErrors('reason');
                $this->assertSame($status, $user->fresh()->status);
            }
        }

        $this->assertDatabaseCount('user_status_changes', 0);
    }

    public function test_status_action_ignores_forged_account_and_audit_fields(): void
    {
        $admin = $this->administrator();
        $user = User::factory()->create(['role' => 'buyer', 'status' => 'active', 'email' => 'unchanged@example.test']);
        $reason = str_repeat('a', 2000);

        $this->actingAs($admin)->post(route('admin.users.suspend', $user), [
            'reason' => $reason, 'status' => 'active', 'role' => 'admin', 'email' => 'changed@example.test',
            'reviewed_by' => $user->id, 'reviewed_at' => '2026-09-01', 'changed_by' => $user->id,
            'previous_status' => 'pending', 'user_id' => $admin->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'status' => 'suspended', 'role' => 'buyer', 'email' => 'unchanged@example.test',
            'reviewed_by' => null, 'reviewed_at' => null,
        ]);
        $this->assertDatabaseHas('user_status_changes', [
            'user_id' => $user->id, 'changed_by' => $admin->id, 'previous_status' => 'active',
            'status' => 'suspended', 'reason' => $reason,
        ]);
        $this->assertDatabaseCount('user_status_changes', 1);
    }

    public function test_suspension_blocks_existing_sessions_and_login_until_reactivation(): void
    {
        $admin = $this->administrator();
        $user = User::factory()->create(['role' => 'buyer', 'status' => 'active']);
        $this->actingAs($user)->get(route('buyer.home'))->assertOk();
        $this->actingAs($admin)->post(route('admin.users.suspend', $user), ['reason' => 'Under investigation'])
            ->assertSessionHasNoErrors();

        $this->actingAs($user->fresh())->get(route('buyer.home'))->assertRedirectToRoute('login');
        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($admin)->post(route('admin.users.reactivate', $user), ['reason' => 'Investigation completed'])
            ->assertSessionHasNoErrors();
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasNoErrors()->assertRedirectToRoute('buyer.home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_unknown_account_ids_return_not_found_without_audit_changes(): void
    {
        $this->actingAs($this->administrator());
        $this->get(route('admin.users.show', 999999))->assertNotFound();
        $this->post(route('admin.users.suspend', 999999), ['reason' => 'Missing account'])->assertNotFound();
        $this->post(route('admin.users.reactivate', 999999), ['reason' => 'Missing account'])->assertNotFound();
        $this->assertDatabaseCount('user_status_changes', 0);
    }
}
