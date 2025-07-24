<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SyncProductInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-products {--force : Force sync even if inventory exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product stock with inventory system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Product-Inventory Sync...');

        $products = Product::all();
        $synced = 0;
        $created = 0;
        $updated = 0;

        foreach ($products as $product) {
            $this->line("Processing: {$product->name}");

            // Get current inventory total
            $inventoryStock = Inventory::where('product_id', $product->id)
                ->where('status', 'available')
                ->sum('quantity');

            // Check if product has stock but no inventory
            if ($product->stock > 0 && $inventoryStock == 0) {
                $this->warn("  → Product has stock ({$product->stock}) but no inventory. Creating inventory...");

                Inventory::create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $product->stock,
                    'unit' => 'pcs',
                    'batch_number' => 'SYNC-' . date('Y-m-d'),
                    'status' => 'available',
                    'received_date' => now(),
                    'supplier_id' => $product->supplier_id,
                    'location' => 'Main Warehouse',
                    'reorder_level' => 10
                ]);

                $created++;
                $this->info("  ✓ Created inventory record with {$product->stock} units");
            } elseif ($product->stock != $inventoryStock) {
                if ($this->option('force') || $this->confirm("  Product stock ({$product->stock}) doesn't match inventory ({$inventoryStock}). Update inventory?")) {

                    if ($inventoryStock > 0) {
                        // Update existing inventory
                        $inventory = Inventory::where('product_id', $product->id)
                            ->where('status', 'available')
                            ->first();

                        if ($inventory) {
                            $inventory->update(['quantity' => $product->stock]);
                            $updated++;
                            $this->info("  ✓ Updated inventory to {$product->stock} units");
                        }
                    } else {
                        // Create new inventory if none exists
                        Inventory::create([
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $product->stock,
                            'unit' => 'pcs',
                            'batch_number' => 'SYNC-' . date('Y-m-d'),
                            'status' => 'available',
                            'received_date' => now(),
                            'supplier_id' => $product->supplier_id,
                            'location' => 'Main Warehouse',
                            'reorder_level' => 10
                        ]);

                        $created++;
                        $this->info("  ✓ Created new inventory with {$product->stock} units");
                    }
                }
            } else {
                $synced++;
                $this->info("  ✓ Already synced");
            }
        }

        $this->info("\n=== Sync Complete ===");
        $this->table(['Action', 'Count'], [
            ['Already Synced', $synced],
            ['Created New', $created],
            ['Updated Existing', $updated],
            ['Total Processed', $products->count()]
        ]);

        return 0;
    }
}
