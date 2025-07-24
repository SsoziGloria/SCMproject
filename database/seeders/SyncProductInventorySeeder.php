<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\InventoryService;

class SyncProductInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Syncing all products with inventory...');

        $products = Product::all();
        $synced = 0;
        $created = 0;

        foreach ($products as $product) {
            $inventoryStock = Inventory::where('product_id', $product->id)
                ->where('status', 'available')
                ->sum('quantity');

            if ($product->stock > 0 && $inventoryStock == 0) {
                // Create inventory for products with stock but no inventory
                Inventory::create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $product->stock,
                    'unit' => 'pcs',
                    'batch_number' => 'SEED-' . date('Y-m-d') . '-' . $product->id,
                    'status' => 'available',
                    'received_date' => now(),
                    'supplier_id' => $product->supplier_id,
                    'location' => 'Main Warehouse',
                    'reorder_level' => rand(5, 15)
                ]);

                $created++;
                $this->command->line("✓ Created inventory for: {$product->name} ({$product->stock} units)");
            } elseif ($product->stock == $inventoryStock) {
                $synced++;
            } else {
                // Sync inventory to match product stock
                if ($inventoryStock > 0) {
                    $inventory = Inventory::where('product_id', $product->id)
                        ->where('status', 'available')
                        ->first();
                    $inventory->quantity = $product->stock;
                    $inventory->save();
                }

                $this->command->line("✓ Synced {$product->name}: {$inventoryStock} → {$product->stock}");
            }
        }

        $this->command->info("Sync complete! Created: {$created}, Already synced: {$synced}");
    }
}
