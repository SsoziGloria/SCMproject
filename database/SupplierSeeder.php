<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::insert([
            [
                'name' => 'Supplier One',
                'contact_email' => 'supplier1@example.com',
                'contact_phone' => '123-456-7890',
                'address' => '123 Main St, Cityville',
                'company' => 'ChocoSupplies Inc.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supplier Two',
                'contact_email' => 'supplier2@example.com',
                'contact_phone' => '987-654-3210',
                'address' => '456 Oak Ave, Townsville',
                'company' => 'SweetGoods Ltd.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}