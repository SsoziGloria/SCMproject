<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if workers already exist
        if (Worker::count() == 0) {
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

            $this->command->info('Workers seeded successfully!');
        } else {
            $this->command->info('Workers already exist in database.');
        }
    }
} 