<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_id' => 'CHOC-DARK-70',
                'name' => 'Dark Chocolate Bar 70%',
                'ingredients' => 'Cocoa mass, sugar, cocoa butter, vanilla',
                'price' => 3.99,
                'description' => 'Rich and intense dark chocolate with 70% cocoa content.',
                'image' => 'images/dark_chocolate.jpg',
                'featured' => true,
                'stock' => 150,
                'supplier_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-MILK-CHIPS',
                'name' => 'Milk Chocolate Chips',
                'ingredients' => 'Sugar, cocoa butter, milk solids, cocoa mass, soy lecithin',
                'price' => 4.25,
                'description' => 'Smooth and sweet milk chocolate chips for baking or snacking.',
                'image' => 'images/milk_chips.jpg',
                'featured' => false,
                'stock' => 80,
                'supplier_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-WHITE-BLOCK',
                'name' => 'White Chocolate Block',
                'ingredients' => 'Sugar, cocoa butter, milk solids, vanilla',
                'price' => 3.75,
                'description' => 'Creamy white chocolate in a convenient 100g block.',
                'image' => 'images/white_block.jpg',
                'featured' => true,
                'stock' => 60,
                'supplier_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}