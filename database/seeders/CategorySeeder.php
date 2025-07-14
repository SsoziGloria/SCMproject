<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Dark Chocolate',
                'description' => 'Rich, intense chocolate with high cocoa content.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Milk Chocolate',
                'description' => 'Smooth chocolate made with milk solids.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'White Chocolate',
                'description' => 'Sweet chocolate made with cocoa butter, sugar, and milk.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cocoa Powder',
                'description' => 'Finely ground cocoa solids for baking and drinks.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chocolate Bars',
                'description' => 'Solid bars of chocolate in various flavors.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Truffles',
                'description' => 'Chocolate confections with creamy centers.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Raw Cocoa',
                'description' => 'Unprocessed cocoa beans and nibs.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}