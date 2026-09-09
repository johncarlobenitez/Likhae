<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin',    'email' => 'admin@likhae.com',    'role' => 'admin',    'status' => 'active'],
            ['name' => 'Buyer',    'email' => 'buyer@likhae.com',    'role' => 'buyer',    'status' => 'active'],
            ['name' => 'Seller',   'email' => 'seller@likhae.com',   'role' => 'seller',   'status' => 'active'],
            ['name' => 'Logistics','email' => 'logistics@likhae.com','role' => 'logistics','status' => 'active'],
            ['name' => 'Rider',    'email' => 'rider@likhae.com',    'role' => 'rider',    'status' => 'active'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('Password1')])
            );
        }
    }
}
