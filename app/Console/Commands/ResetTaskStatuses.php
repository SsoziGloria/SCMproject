<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class ResetTaskStatuses extends Command
{
    protected $signature = 'tasks:reset-status';
    protected $description = 'Resets the daily status of all tasks from "staffed" back to "pending"';

    public function handle()
    {
        $this->info('Resetting daily task statuses...');

        $count = Task::where('status_for_day', 'staffed')
            ->update(['status_for_day' => 'pending']);

        $this->info("Done. Reset {$count} tasks.");
        return 0;
    }
}
