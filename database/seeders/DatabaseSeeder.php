<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) return;

        DB::transaction(function (): void {
            $now = now();
            foreach (['buyer' => 'Buyer', 'seller' => 'Seller', 'admin' => 'Administrator', 'logistics' => 'Logistics', 'rider' => 'Rider'] as $code => $name) {
                DB::table('roles')->updateOrInsert(['code' => $code], ['name' => $name, 'description' => $name.' account role.', 'created_at' => $now, 'updated_at' => $now]);
            }

            $province = $this->upsert('geo_provinces', ['code' => 'PH-DEV'], ['name' => 'Development Province', 'created_at' => $now, 'updated_at' => $now]);
            $municipality = $this->upsert('geo_municipalities', ['code' => 'PH-DEV-CITY'], ['province_id' => $province, 'name' => 'Development City', 'type' => 'CITY', 'created_at' => $now, 'updated_at' => $now]);
            $barangay = $this->upsert('geo_barangays', ['code' => 'PH-DEV-BRGY'], ['municipality_id' => $municipality, 'name' => 'Development Barangay', 'postal_code' => '1000', 'created_at' => $now, 'updated_at' => $now]);

            $accounts = [
                ['admin@likhae.com', 'LIKHAE', 'Admin', ['admin']],
                ['buyer@likhae.com', 'LIKHAE', 'Buyer', ['buyer']],
                ['seller@likhae.com', 'LIKHAE', 'Seller', ['buyer', 'seller']],
                ['logistics@likhae.com', 'LIKHAE', 'Logistics', ['buyer', 'logistics']],
                ['rider@likhae.com', 'LIKHAE', 'Rider', ['rider']],
                ['maria.santos@example.com', 'Maria', 'Santos', ['buyer']],
            ];
            $users = $addresses = [];
            foreach ($accounts as $index => [$email, $first, $last, $roleCodes]) {
                $users[$email] = $this->upsert('users', ['email' => $email], [
                    'first_name' => $first, 'middle_initial' => null, 'last_name' => $last,
                    'sex' => $index % 2 ? 'FEMALE' : 'MALE', 'contact_number' => '0917000000'.$index,
                    'birthday' => '1990-01-01', 'email_verified_at' => $now, 'password' => Hash::make('Password1'),
                    'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now,
                ]);
                foreach ($roleCodes as $code) {
                    DB::table('user_roles')->updateOrInsert(
                        ['user_id' => $users[$email], 'role_id' => DB::table('roles')->where('code', $code)->value('id')],
                        ['assigned_by_user_id' => null, 'is_active' => true, 'assigned_at' => $now, 'revoked_at' => null, 'created_at' => $now, 'updated_at' => $now]
                    );
                }
                $addresses[$email] = $this->upsert('addresses', ['user_id' => $users[$email], 'label' => 'Primary'], [
                    'barangay_id' => $barangay, 'recipient_name' => "$first $last", 'contact_number' => '0917000000'.$index,
                    'street_address' => '1 LIKHAE Development Street', 'landmark' => 'Development seed address',
                    'latitude' => null, 'longitude' => null, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }

            $metroManila = $this->upsert('geo_provinces', ['code' => 'PH-00'], ['name' => 'Metro Manila', 'created_at' => $now, 'updated_at' => $now]);
            $quezonCity = $this->upsert('geo_municipalities', ['code' => 'PH-137404'], ['province_id' => $metroManila, 'name' => 'Quezon City', 'type' => 'CITY', 'created_at' => $now, 'updated_at' => $now]);
            $bagumbayan = $this->upsert('geo_barangays', ['code' => 'PH-137404006'], ['municipality_id' => $quezonCity, 'name' => 'Bagumbayan', 'postal_code' => '1110', 'created_at' => $now, 'updated_at' => $now]);
            $realBuyerId = $users['maria.santos@example.com'];
            DB::table('users')->where('id', $realBuyerId)->update([
                'middle_initial' => 'L', 'sex' => 'FEMALE', 'contact_number' => '09171234567',
                'birthday' => '1996-04-18', 'updated_at' => $now,
            ]);
            DB::table('addresses')->where('id', $addresses['maria.santos@example.com'])->update([
                'barangay_id' => $bagumbayan, 'recipient_name' => 'Maria L. Santos',
                'contact_number' => '09171234567', 'street_address' => 'Unit 8B, 42 Eastwood Avenue',
                'landmark' => 'Near Eastwood Mall', 'latitude' => 14.6103000, 'longitude' => 121.0803000,
                'updated_at' => $now,
            ]);

            $buyerRoleId = DB::table('roles')->where('code', 'buyer')->value('id');
            $applicationId = $this->upsert('registration_applications', ['application_number' => 'REG-BUYER-0001'], [
                'user_id' => $realBuyerId, 'role_id' => $buyerRoleId, 'status' => 'APPROVED',
                'reviewed_by_user_id' => $users['admin@likhae.com'], 'submitted_at' => $now->copy()->subDay(),
                'reviewed_at' => $now, 'decision_notes' => 'Approved development buyer fixture.',
                'rejection_reason' => null, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $idPath = 'registration/seed-buyers/maria-santos-valid-id.txt';
            Storage::disk('registrations')->put($idPath, "Development-only identity document fixture for Maria L. Santos.\n");
            DB::table('application_documents')->updateOrInsert(
                ['registration_application_id' => $applicationId, 'document_type' => 'VALID_ID'],
                ['file_path' => $idPath, 'original_name' => 'maria-santos-valid-id.txt', 'mime_type' => 'text/plain',
                    'verification_status' => 'VERIFIED', 'verified_by_user_id' => $users['admin@likhae.com'],
                    'verified_at' => $now, 'rejection_reason' => null, 'created_at' => $now, 'updated_at' => $now]
            );

            $category = $this->upsert('categories', ['slug' => 'general-merchandise'], ['parent_id' => null, 'name' => 'General Merchandise', 'description' => 'General marketplace products.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            $this->upsert('seller_profiles', ['user_id' => $users['seller@likhae.com']], [
                'primary_category_id' => $category, 'business_address_id' => $addresses['seller@likhae.com'], 'business_name' => 'LIKHAE Seller Shop',
                'business_registration_number' => 'SEED-SELLER-001', 'status' => 'ACTIVE', 'approved_by_user_id' => $users['admin@likhae.com'], 'approved_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $center = $this->upsert('logistics_centers', ['code' => 'LKH-SEED-001'], [
                'owner_user_id' => $users['logistics@likhae.com'], 'address_id' => $addresses['logistics@likhae.com'], 'business_name' => 'LIKHAE Logistics',
                'business_registration_number' => 'SEED-LOG-001', 'dti_registration_number' => 'SEED-DTI-001', 'status' => 'ACTIVE',
                'approved_by_user_id' => $users['admin@likhae.com'], 'approved_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('logistics_center_users')->updateOrInsert(['logistics_center_id' => $center, 'user_id' => $users['logistics@likhae.com']], ['position' => 'Owner', 'is_active' => true, 'joined_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
            $this->upsert('rider_profiles', ['user_id' => $users['rider@likhae.com']], [
                'logistics_center_id' => $center, 'vehicle_type' => 'MOTORCYCLE', 'plate_number' => 'SEED-001', 'drivers_license_number' => 'SEED-DL-001',
                'status' => 'ACTIVE', 'approved_by_user_id' => $users['logistics@likhae.com'], 'approved_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
            foreach (['commission_rate' => ['0.10', 'DECIMAL'], 'return_window_days' => ['7', 'INTEGER'], 'platform_name' => ['LIKHAE', 'STRING']] as $key => [$value, $type]) {
                DB::table('platform_settings')->updateOrInsert(['key' => $key], ['value' => $value, 'value_type' => $type, 'updated_by_user_id' => $users['admin@likhae.com'], 'created_at' => $now, 'updated_at' => $now]);
            }
        });

        $this->call(LikhaeSellerTestAccountsSeeder::class);
    }

    private function upsert(string $table, array $identity, array $values): int
    {
        DB::table($table)->updateOrInsert($identity, $values);
        return (int) DB::table($table)->where($identity)->value('id');
    }
}
