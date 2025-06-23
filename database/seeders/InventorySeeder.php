<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inventories')->insert([
            [
                'product_id' => 1,
                'product_name' => 'Dark Chocolate Bar 70%',
                'quantity' => 150,
                'unit' => 'pcs',
                'batch_number' => 'BATCH-DK-001',
                'status' => 'available',
                'received_date' => now()->subDays(5),
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
                'batch_number' => 'BATCH-MC-002',
                'status' => 'available',
                'received_date' => now()->subDays(10),
                'expiration_date' => now()->addMonths(6),
                'location' => 'Warehouse B',
                'supplier_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'product_name' => 'White Chocolate Blocks',
                'quantity' => 60,
                'unit' => 'pcs',
                'batch_number' => 'BATCH-WH-003',
                'status' => 'reserved',
                'received_date' => now()->subDays(2),
                'expiration_date' => now()->addMonths(12),
                'location' => 'Cold Storage',
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}