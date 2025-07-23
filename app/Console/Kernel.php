<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendStockAlerts::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        $schedule->command('stock:alert')->everyMinute();
        $schedule->command('tasks:reset-status')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        // Automatically load all commands in app/Console/Commands
        $this->load(__DIR__ . '/Commands');

        // Load the console routes file
        require base_path('routes/console.php');
    }
}
