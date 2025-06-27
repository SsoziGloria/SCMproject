<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductWithInventorySeeder extends Seeder
{
    public function run(): void
    {
        // Seed products
        $products = [
            [
                'id' => 1,
                'product_id' => 'CHOC-DARK-70',
                'name' => 'Dark Chocolate Bar 70%',
                'ingredients' => 'Cocoa mass, sugar, cocoa butter, vanilla',
                'price' => 3.99,
                'description' => 'Rich and intense dark chocolate.',
                'image' => 'images/dark.jpg',
                'featured' => true,
                'stock' => 150,
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'product_id' => 'CHOC-MILK-CHIPS',
                'name' => 'Milk Chocolate Chips',
                'ingredients' => 'Sugar, cocoa butter, milk solids, cocoa mass',
                'price' => 4.25,
                'description' => 'Smooth milk chocolate for snacking.',
                'image' => 'images/milk.jpg',
                'featured' => false,
                'stock' => 80,
                'supplier_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);

        // Seed inventory using product IDs
        DB::table('inventories')->insert([
            [
                'product_id' => 1,
                'product_name' => 'Dark Chocolate Bar 70%',
                'quantity' => 150,
                'unit' => 'pcs',
                'batch_number' => 'BATCH-001',
                'status' => 'available',
                'received_date' => now()->subDays(3),
                'expiration_date' => now()->addMonths(10),
                'location' => 'Warehouse A',
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'product_name' => 'Milk Chocolate Chips',
                'quantity' => 80,
                'unit' => 'kg',
                'batch_number' => 'BATCH-002',
                'status' => 'available',
                'received_date' => now()->subDays(5),
                'expiration_date' => now()->addMonths(6),
                'location' => 'Warehouse B',
                'supplier_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}