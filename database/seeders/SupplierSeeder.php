<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Supplier;
use App\Models\Supplier as SupplierModel;


class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_id' => 3,
                'name' => 'Kampala Cocoa Ltd',
                'email' => 'info@kampalacocoa.com',
                'phone' => '+256700000001',
                'address' => 'Plot 12, Cocoa Road, Kampala',
                'company' => 'Kampala Cocoa Ltd',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_id' => 3,
                'name' => 'ChocoFarmers Co-op',
                'email' => 'contact@chocofarmers.coop',
                'phone' => '+256700000002',
                'address' => 'Farm Lane, Hoima',
                'company' => 'ChocoFarmers Cooperative',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_id' => 3,
                'name' => 'WestNile Beans',
                'email' => 'beans@westnile.com',
                'phone' => '+256700000003',
                'address' => 'Main Street, Arua',
                'company' => 'WestNile Beans Ltd',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($suppliers as $supplier) {
            SupplierModel::firstOrCreate(
                ['email' => $supplier['email']],
                $supplier
            );
        }
    }
}