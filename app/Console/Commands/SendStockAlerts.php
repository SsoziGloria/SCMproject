<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\StockAlert;
use Illuminate\Support\Facades\Mail;
use App\Models\Inventory;


class SendStockAlerts extends Command
{
    protected $signature = 'stock:alert';
    protected $description = 'Send stock alert emails for low inventory items';

    public function handle()
    {
        $lowStock = Inventory::with('product')
        ->where('quantity', '<', 10)
        ->get();

    if ($lowStock->isEmpty()) {
        $this->info('No low stock items.');
        return;
    }
    
    Mail::to('irenemargi256@gmail.com')->send(new StockAlert($lowStock));
        $this->info('Stock alert email sent successfully.');
    }
}
    /**
     * Execute the console command.
     */


