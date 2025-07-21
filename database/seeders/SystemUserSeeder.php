<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'system@chocolatescm'],
            [
                'name' => 'System Alerts',
                'password' => Hash::make(Str::random(15)),
                'role' => 'system',
            ]
        );
    }
}
