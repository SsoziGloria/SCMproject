<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'Dark Chocolate Bar',
                'description' => 'Rich dark chocolate made from premium cocoa beans.',
                'category' => 'Chocolate',
                'price' => 4.99,
                'quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Milk Chocolate Truffles',
                'description' => 'Smooth milk chocolate truffles with a creamy center.',
                'category' => 'Truffles',
                'price' => 7.50,
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'White Chocolate Chips',
                'description' => 'Perfect for baking and snacking.',
                'category' => 'Baking',
                'price' => 3.25,
                'quantity' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}