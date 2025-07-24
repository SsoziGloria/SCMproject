<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class SyncProductStockWithInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-stock 
                            {--product= : Sync specific product by ID}
                            {--show-mismatches : Only show products with mismatched stock}
                            {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product stock to match total inventory quantities across all locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->option('product');
        $showMismatches = $this->option('show-mismatches');
        $dryRun = $this->option('dry-run');

        if ($productId) {
            $products = Product::where('id', $productId)->get();
            if ($products->isEmpty()) {
                $this->error("Product with ID {$productId} not found.");
                return 1;
            }
        } else {
            $products = Product::with('inventories')->get();
        }

        $this->info('=== Product Stock vs Inventory Sync Report ===');
        $this->newLine();

        $synced = 0;
        $mismatched = 0;
        $totalProducts = $products->count();

        $headers = ['Product ID', 'Product Name', 'Current Stock', 'Total Inventory', 'Status', 'Action'];
        $tableData = [];

        foreach ($products as $product) {
            $currentStock = $product->stock;
            $totalInventory = $product->getTotalInventoryQuantity();
            $isMatched = $currentStock == $totalInventory;

            if (!$isMatched) {
                $mismatched++;
            }

            // Skip matched products if only showing mismatches
            if ($showMismatches && $isMatched) {
                continue;
            }

            $status = $isMatched ? '✅ Synced' : '❌ Mismatch';
            $action = 'No change needed';

            if (!$isMatched) {
                $action = $dryRun ?
                    "Would update stock: {$currentStock} → {$totalInventory}" :
                    "Updating stock: {$currentStock} → {$totalInventory}";

                if (!$dryRun) {
                    $product->syncStockToInventory();
                    $synced++;
                    $status = '🔄 Updated';
                }
            }

            $tableData[] = [
                $product->id,
                substr($product->name, 0, 30) . (strlen($product->name) > 30 ? '...' : ''),
                $currentStock,
                $totalInventory,
                $status,
                $action
            ];
        }

        if (!empty($tableData)) {
            $this->table($headers, $tableData);
        } else {
            $this->info('No products to display with current filters.');
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Total products examined: {$totalProducts}");
        $this->info("Products with mismatched stock: {$mismatched}");

        if ($dryRun) {
            $this->warn("DRY RUN - No changes were made");
            $this->info("Products that would be synced: {$mismatched}");
        } else {
            $this->info("Products synced: {$synced}");
        }

        if ($mismatched > 0 && !$dryRun) {
            $this->newLine();
            $this->warn('⚠️  Stock synchronization completed. Please verify the changes.');
        }

        return 0;
    }
}
