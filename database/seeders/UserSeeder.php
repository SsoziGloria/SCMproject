<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users =
            [

                [
                    'name' => 'Customer',
                    'email' => 'customer@c',
                    'password' => Hash::make('pppppp'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Retailer',
                    'email' => 'retailer@r',
                    'password' => Hash::make('pppppp'),
                    'role' => 'retailer',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Retailer',
                    'email' => 'retailer2@r',
                    'password' => Hash::make('pppppp'),
                    'role' => 'retailer',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Supplier',
                    'email' => 'supplier@s',
                    'password' => Hash::make('pppppp'),
                    'role' => 'supplier',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'name' => 'Supplier2',
                    'email' => 'supplier2@s',
                    'password' => Hash::make('pppppp'),
                    'role' => 'supplier',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Admin',
                    'email' => 'admin@a',
                    'password' => Hash::make('pppppp'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
