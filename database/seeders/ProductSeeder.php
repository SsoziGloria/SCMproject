<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        // Create or get supplier users
        $supplier1 = User::firstOrCreate(
            ['email' => 'supplier1@example.com'],
            ['name' => 'Supplier One', 'password' => bcrypt('password'), 'role' => 'supplier']
        );
        $supplier2 = User::firstOrCreate(
            ['email' => 'supplier2@example.com'],
            ['name' => 'Supplier Two', 'password' => bcrypt('password'), 'role' => 'supplier']
        );

        DB::table('products')->insert([
            [
                'product_id' => 'CHOC-DARK-70',
                'name' => 'Dark Chocolate Bar 70%',
                'ingredients' => 'Cocoa mass, sugar, cocoa butter, vanilla',
                'price' => 6000,
                'description' => 'Rich and intense dark chocolate with 70% cocoa content.',
                'image' => 'images/dark_chocolate.jpg',
                'featured' => true,
                'stock' => 150,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-MILK-CHIPS',
                'name' => 'Milk Chocolate Chips',
                'ingredients' => 'Sugar, cocoa butter, milk solids, cocoa mass, soy lecithin',
                'price' => 8000,
                'description' => 'Smooth and sweet milk chocolate chips for baking or snacking.',
                'image' => 'images/milk_chips.jpg',
                'featured' => false,
                'stock' => 80,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-WHITE-BLOCK',
                'name' => 'White Chocolate Block',
                'ingredients' => 'Sugar, cocoa butter, milk solids, vanilla',
                'price' => 4000,
                'description' => 'Creamy white chocolate in a convenient 100g block.',
                'image' => 'images/white_block.jpg',
                'featured' => true,
                'stock' => 60,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-DRINK-CLASSIC',
                'name' => 'Drinking Chocolate',
                'ingredients' => 'Sugar, cocoa powder, salt, natural flavor',
                'price' => 5000,
                'description' => 'Classic drinking chocolate for a rich and creamy beverage.',
                'image' => 'images/drinking_chocolate.jpg',
                'featured' => false,
                'stock' => 120,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-ALMOND-COAT',
                'name' => 'Choco Coated Almonds',
                'ingredients' => 'Almonds, dark chocolate, sugar, cocoa butter',
                'price' => 9500,
                'description' => 'Crunchy almonds coated in premium dark chocolate.',
                'image' => 'images/choco_almonds.jpg',
                'featured' => true,
                'stock' => 70,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-ECLAIRS',
                'name' => 'Eclairs',
                'ingredients' => 'Sugar, glucose syrup, milk solids, cocoa solids, butter',
                'price' => 3000,
                'description' => 'Soft caramel eclairs with a chocolate center.',
                'image' => 'images/eclairs.jpg',
                'featured' => false,
                'stock' => 100,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-SYRUP-ORG',
                'name' => 'Organic Choco Syrup',
                'ingredients' => 'Organic cocoa, organic sugar, water, natural vanilla',
                'price' => 7000,
                'description' => 'Organic chocolate syrup for desserts and beverages.',
                'image' => 'images/organic_choco_syrup.jpg',
                'featured' => true,
                'stock' => 90,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'CHOC-DARK-BITES',
                'name' => 'Dark Choco Bites',
                'ingredients' => 'Cocoa mass, sugar, cocoa butter, soy lecithin',
                'price' => 6500,
                'description' => 'Bite-sized pieces of intense dark chocolate.',
                'image' => 'images/dark_choco_bites.jpg',
                'featured' => false,
                'stock' => 110,
                'supplier_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}