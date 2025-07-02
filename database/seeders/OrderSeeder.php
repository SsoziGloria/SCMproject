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
                'supplier_id' => $supplier->id,
                'total_amount' => 150000,
                'status' => 'pending',
                'shipping_address' => 'Jinja, Uganda',
                'quantity' => 2,
                'ordered_at' => now()->subDays(2),
                'delivered_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
                'total_amount' => 180000,
                'status' => 'shipped',
                'shipping_address' => 'Kampala, Uganda',
                'quantity' => 3,
                'ordered_at' => now()->subDays(5),
                'delivered_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}