<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Sample User', 'password' => bcrypt('password'), 'role' => 'retailer']
        );

        // Create or get a supplier user
        $supplier = User::firstOrCreate(
            ['email' => 'supplier@example.com'],
            ['name' => 'Supplier User', 'password' => bcrypt('password'), 'role' => 'supplier']
        );

        Order::insert([
            [
                'order_number' => strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'total_amount' => 150000,
                'status' => 'pending',
                'payment' => 'bank_transfer',
                'payment_status' => 'pending',
                'shipping_address' => 'Driver\'s Avenue',
                'shipping_city' => 'Jinja',
                'shipping_country' => 'UG',
                'delivered_at' => null,
                'notes' => 'Please deliver by next week.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'total_amount' => 180000,
                'status' => 'shipped',
                'payment' => 'mobile_money',
                'payment_status' => 'paid',
                'shipping_address' => 'Main Street',
                'shipping_city' => 'Kampala',
                'shipping_country' => 'UG',
                'delivered_at' => now()->subDay(),
                'notes' => 'Handle with care.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}