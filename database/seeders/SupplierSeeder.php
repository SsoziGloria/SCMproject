<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'supplier_id' => 3,
                'name' => 'Kampala Cocoa Ltd',
                'email' => 'info@kampalacocoa.com',
                'phone' => '+256700000001',
                'address' => 'Plot 12, Industrial Area, Kampala',
                'company' => 'Kampala Cocoa Ltd',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_id' => 3,
                'name' => 'Rwenzori Beans',
                'email' => 'contact@rwenzoribeans.com',
                'phone' => '+256700000002',
                'address' => 'Rwenzori Mountains, Kasese',
                'company' => 'Rwenzori Beans',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_id' => 3,
                'name' => 'Nile Agro Supplies',
                'email' => 'sales@nileagro.com',
                'phone' => '+256700000003',
                'address' => 'Jinja Road, Jinja',
                'company' => 'Nile Agro Supplies',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}