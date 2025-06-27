<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $product1 = \App\Models\Product::where('product_id', 'CHOC-DARK-70')->first();
        $product2 = \App\Models\Product::where('product_id', 'CHOC-MILK-CHIPS')->first();
        $product3 = \App\Models\Product::where('product_id', 'CHOC-WHITE-BLOCK')->first();

        \DB::table('inventories')->insert([
            [
                'product_id' => $product1->id,
                'product_name' => $product1->name,
                'quantity' => 150,
                'unit' => 'pcs',
                'batch_number' => 'BATCH-DK-001',
                'status' => 'available',
                'received_date' => now()->subDays(5),
                'expiration_date' => now()->addMonths(10),
                'location' => 'Warehouse A',
                'supplier_id' => $product1->supplier_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $product2->id,
                'product_name' => $product2->name,
                'quantity' => 80,
                'unit' => 'kg',
                'batch_number' => 'BATCH-MC-002',
                'status' => 'available',
                'received_date' => now()->subDays(10),
                'expiration_date' => now()->addMonths(6),
                'location' => 'Warehouse B',
                'supplier_id' => $product2->supplier_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $product3->id,
                'product_name' => $product3->name,
                'quantity' => 60,
                'unit' => 'pcs',
                'batch_number' => 'BATCH-WH-003',
                'status' => 'reserved',
                'received_date' => now()->subDays(2),
                'expiration_date' => now()->addMonths(12),
                'location' => 'Cold Storage',
                'supplier_id' => $product3->supplier_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}