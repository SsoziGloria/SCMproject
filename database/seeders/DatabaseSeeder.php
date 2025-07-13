<?php
namespace Database\Seeders;
use App\Models\User;// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Worker;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            InventorySeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
         // Create sample workers
        Worker::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'position' => 'Developer'
        ]);
        Worker::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '098-765-4321',
            'position' => 'Designer'
        ]);

        Worker::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'phone' => '555-123-4567',
            'position' => 'Manager'
        ]);


    }
}




