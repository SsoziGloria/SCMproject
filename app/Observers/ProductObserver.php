<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // When a new product is created with stock, create corresponding inventory
        if ($product->stock > 0) {
            Inventory::create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $product->stock,
                'unit' => 'pcs',
                'batch_number' => 'NEW-' . date('Y-m-d') . '-' . $product->id,
                'status' => 'available',
                'received_date' => now(),
                'supplier_id' => $product->supplier_id,
                'location' => 'Main Warehouse',
                'reorder_level' => 10
            ]);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Skip observer sync if flag is set (during order processing)
        if (Product::$skipObserverSync) {
            return;
        }

        // Only sync if stock was actually changed
        if ($product->isDirty('stock')) {
            $oldStock = $product->getOriginal('stock');
            $newStock = $product->stock;
            $stockDiff = $newStock - $oldStock;

            // Calculate current total inventory across all locations
            $currentInventoryTotal = $product->inventories()
                ->where('status', 'available')
                ->sum('quantity');

            // If product stock doesn't match inventory total, we need to sync
            if ($newStock != $currentInventoryTotal) {
                $this->syncProductStockWithInventory($product, $newStock, $currentInventoryTotal, $stockDiff);
            }
        }        // Also sync product name changes
        if ($product->isDirty('name')) {
            Inventory::where('product_id', $product->id)
                ->update(['product_name' => $product->name]);
        }
    }

    /**
     * Sync product stock with inventory across multiple locations
     */
    private function syncProductStockWithInventory($product, $newStock, $currentTotal, $stockDiff)
    {
        $availableInventories = $product->inventories()
            ->where('status', 'available')
            ->orderBy('updated_at', 'desc') // Most recently updated first
            ->get();

        if ($availableInventories->isEmpty()) {
            // No inventory records exist, create one if stock > 0
            if ($newStock > 0) {
                $inventory = Inventory::create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $newStock,
                    'unit' => 'pcs',
                    'batch_number' => 'ADJ-' . date('Y-m-d') . '-' . $product->id,
                    'status' => 'available',
                    'received_date' => now(),
                    'supplier_id' => $product->supplier_id,
                    'location' => 'Main Warehouse',
                    'reorder_level' => 10
                ]);

                // Create adjustment record
                InventoryAdjustment::create([
                    'inventory_id' => $inventory->id,
                    'adjustment_type' => 'increase',
                    'quantity_change' => $newStock,
                    'reason' => 'Product stock sync - new inventory created',
                    'notes' => "Created inventory to match product stock of {$newStock}",
                    'user_id' => Auth::id(),
                    'user_name' => Auth::check() ? Auth::user()->name : 'System'
                ]);
            }
            return;
        }

        // Multiple inventory locations exist - distribute the difference intelligently
        if ($stockDiff > 0) {
            // Stock increased - add to the most recently updated location
            $primaryInventory = $availableInventories->first();
            $primaryInventory->quantity += $stockDiff;
            $primaryInventory->save();

            InventoryAdjustment::create([
                'inventory_id' => $primaryInventory->id,
                'adjustment_type' => 'increase',
                'quantity_change' => $stockDiff,
                'reason' => 'Product stock increase sync',
                'notes' => "Stock increased by {$stockDiff} units (distributed to {$primaryInventory->location})",
                'user_id' => Auth::id(),
                'user_name' => Auth::check() ? Auth::user()->name : 'System'
            ]);
        } elseif ($stockDiff < 0) {
            // Stock decreased - reduce from locations (FIFO style)
            $remaining = abs($stockDiff);

            foreach ($availableInventories as $inventory) {
                if ($remaining <= 0) break;

                $reduceAmount = min($inventory->quantity, $remaining);
                $inventory->quantity -= $reduceAmount;
                $remaining -= $reduceAmount;

                if ($inventory->quantity <= 0) {
                    $inventory->status = 'depleted';
                }
                $inventory->save();

                InventoryAdjustment::create([
                    'inventory_id' => $inventory->id,
                    'adjustment_type' => 'decrease',
                    'quantity_change' => -$reduceAmount,
                    'reason' => 'Product stock decrease sync',
                    'notes' => "Stock reduced by {$reduceAmount} units from {$inventory->location}",
                    'user_id' => Auth::id(),
                    'user_name' => Auth::check() ? Auth::user()->name : 'System'
                ]);
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        // When product is deleted, mark inventory as unavailable
        Inventory::where('product_id', $product->id)
            ->update(['status' => 'unavailable']);
    }
}
