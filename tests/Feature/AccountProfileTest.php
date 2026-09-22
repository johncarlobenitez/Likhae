<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_update_profile_without_changing_roles(): void
    {
        $user = User::factory()->create(['status' => 'active', 'password' => 'Password1']);
        $user->grant('buyer');
        $user->grant('seller');

        $this->actingAs($user)->put(route('buyer.account.profile.update'), [
            'name' => 'Updated Buyer', 'email' => 'updated@example.test', 'phone' => '09171234567',
            'birthday' => '1995-05-10', 'gender' => 'female',
        ])->assertSessionHasNoErrors()->assertSessionHas('buyer_notice');

        $user->refresh();
        $this->assertSame('Updated Buyer', $user->name);
        $this->assertSame('updated@example.test', $user->email);
        $this->assertTrue($user->hasRole('buyer'));
        $this->assertTrue($user->hasRole('seller'));
    }

    public function test_password_update_requires_current_password_and_rotates_hash(): void
    {
        $user = User::factory()->create(['status' => 'active', 'password' => 'Password1']);
        $user->grant('buyer');

        $this->actingAs($user)->put(route('buyer.account.password.update'), [
            'current_password' => 'wrong', 'new_password' => 'NewPassword2', 'new_password_confirmation' => 'NewPassword2',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Password1', $user->fresh()->password));

        $this->actingAs($user)->put(route('buyer.account.password.update'), [
            'current_password' => 'Password1', 'new_password' => 'NewPassword2', 'new_password_confirmation' => 'NewPassword2',
        ])->assertSessionHasNoErrors()->assertSessionHas('buyer_notice');
        $this->assertTrue(Hash::check('NewPassword2', $user->fresh()->password));
        $this->assertTrue($user->fresh()->hasRole('buyer'));
    }
}
