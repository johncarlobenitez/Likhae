<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\PlatformSetting;
use App\Models\LogisticsProvider;
use App\Models\Rider;
use App\Models\Seller;
use App\Models\ServiceArea;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $accounts = [
            ['name' => 'LIKHAE Admin', 'email' => 'admin@likhae.com', 'roles' => ['admin']],
            ['name' => 'LIKHAE Buyer', 'email' => 'buyer@likhae.com', 'roles' => ['buyer']],
            ['name' => 'LIKHAE Seller', 'email' => 'seller@likhae.com', 'roles' => ['buyer', 'seller']],
            ['name' => 'LIKHAE Logistics', 'email' => 'logistics@likhae.com', 'roles' => ['buyer', 'logistics']],
            ['name' => 'LIKHAE Rider', 'email' => 'rider@likhae.com', 'roles' => ['rider']],
        ];

        $roles = collect(['buyer', 'seller', 'logistics', 'rider', 'admin'])
            ->mapWithKeys(fn (string $name) => [$name => Role::firstOrCreate(['name' => $name])]);

        foreach ($accounts as $account) {
            $roleNames = $account['roles'];
            unset($account['roles']);

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                $account + [
                    'password' => Hash::make('Password1'),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'is_suspended' => false,
                ],
            );

            $user->roles()->sync(collect($roleNames)->map(fn (string $name) => $roles[$name]->id));
        }

        $sellerOwner = User::where('email', 'seller@likhae.com')->sole();
        Seller::updateOrCreate(['user_id' => $sellerOwner->id], [
            'name' => 'LIKHAE Seller Shop', 'slug' => 'likhae-seller-shop', 'status' => 'approved',
            'approved_at' => now(), 'commission_bps' => 800,
        ]);

        $providerOwner = User::where('email', 'logistics@likhae.com')->sole();
        $provider = LogisticsProvider::updateOrCreate(['user_id' => $providerOwner->id], [
            'name' => 'LIKHAE Logistics', 'slug' => 'likhae-logistics', 'status' => 'approved',
            'approved_at' => now(), 'contact_phone' => '09170000000',
        ]);
        ServiceArea::updateOrCreate([
            'logistics_provider_id' => $provider->id,
            'province' => 'Laguna',
            'city' => 'Pila',
        ], [
            'province_code' => '0403400000',
            'city_code' => '0403422000',
            'base_fee_minor' => 7500,
            'per_kg_fee_minor' => 1500,
            'is_active' => true,
        ]);
        $riderUser = User::where('email', 'rider@likhae.com')->sole();
        Rider::updateOrCreate(['user_id' => $riderUser->id], [
            'logistics_provider_id' => $provider->id, 'vehicle_type' => 'motorcycle',
            'plate_no' => 'SEED-001', 'is_active' => true,
        ]);

        foreach ([
            'default_commission_bps' => 800,
            'cod_limit_minor' => 500000,
            'return_window_days' => 7,
            'platform_name' => 'LIKHAE',
        ] as $key => $value) {
            PlatformSetting::put($key, $value, is_int($value) ? 'integer' : 'string');
        }

        $this->call(LikhaeSellerTestAccountsSeeder::class);
    }
}
