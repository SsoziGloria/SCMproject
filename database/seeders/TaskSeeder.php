<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            [
                'name' => 'cocoa_processing',
                'description' => 'Process raw cocoa beans into chocolate liquor',
                'required_workers' => 3,
                'location' => 'Factory A',
                'priority' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'packaging',
                'description' => 'Package finished chocolate products',
                'required_workers' => 2,
                'location' => 'Factory A',
                'priority' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'quality_control',
                'description' => 'Inspect and test chocolate quality',
                'required_workers' => 1,
                'location' => 'QC Lab',
                'priority' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
