<?php

namespace Database\Seeders;

use App\Models\SalesChannel;
use App\Models\Task;
use App\Models\User; // use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Worker;



use Illuminate\Database\Seeder;
use PHPUnit\Event\Telemetry\System;
use Workbench\App\Models\Admin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemUserSeeder::class,
            UserSeeder::class,
            AdminConversationSeeder::class,
            SupplierSeeder::class,
            VendorSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            InventorySeeder::class,
            OrderSeeder::class,
            SalesChannelSeeder::class,
            OrderItemSeeder::class,
            CustomerClusterSummarySeeder::class,
            TaskSeeder::class,
            // ComprehensiveBusinessDataSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // Create sample workers
        Worker::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0778-123-291',
            'position' => 'Head Farmer'
        ]);
        Worker::create([
            'name' => 'Moses Bira',
            'email' => 'mosesbira@gmail.com',
            'phone' => '0772-765-4331',
            'position' => 'Assistant'
        ]);

        Worker::create([
            'name' => 'Micah Tumwesigye',
            'email' => 'micaht@gmail.com',
            'phone' => '0754-623-067',
            'position' => 'Manager'
        ]);
    }
}
