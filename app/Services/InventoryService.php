<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Reduce both product stock and inventory when order is placed
     */
    public function reduceStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->reduceProductAndInventory(
                    $item->product_id,
                    $item->quantity,
                    "Order placed: {$order->order_number}",
                    $order
                );
            }
        });
    }

    /**
     * Restore both product stock and inventory when order is cancelled
     */
    public function restoreStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->restoreProductAndInventory(
                    $item->product_id,
                    $item->quantity,
                    "Order cancelled: {$order->order_number}",
                    $order
                );
            }
        });
    }

    /**
     * Check if there's enough stock for all items in an order
     */
    public function checkOrderStockAvailability(array $items): array
    {
        $issues = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                $issues[] = "Product not found: {$item['product_id']}";
                continue;
            }

            // Check both product stock and inventory
            $productStock = $product->stock;
            $inventoryStock = $this->getAvailableInventoryStock($product->id);

            $actualAvailable = min($productStock, $inventoryStock);

            if ($actualAvailable < $item['quantity']) {
                $issues[] = "Not enough stock for {$product->name}. Requested: {$item['quantity']}, Available: {$actualAvailable}";
            }
        }

        return $issues;
    }

    /**
     * Get available inventory stock for a product
     */
    public function getAvailableInventoryStock(int $productId): int
    {
        return Inventory::where('product_id', $productId)
            ->where('status', 'available')
            ->sum('quantity');
    }

    /**
     * Sync product stock with inventory (one-way: inventory -> product)
     */
    public function syncProductWithInventory(Product $product): void
    {
        $inventoryStock = $this->getAvailableInventoryStock($product->id);

        if ($product->stock !== $inventoryStock) {
            $oldStock = $product->stock;
            $product->stock = $inventoryStock;
            $product->save();

            // Don't trigger observer again for this sync
            $product->unsetEventDispatcher();
            $product->saveQuietly();
        }
    }

    /**
     * Reduce both product stock and inventory atomically
     */
    private function reduceProductAndInventory(int $productId, int $quantity, string $reason, ?Order $order = null): void
    {
        $product = Product::findOrFail($productId);

        // Check availability first
        $productStock = $product->stock;
        $inventoryStock = $this->getAvailableInventoryStock($productId);
        $actualAvailable = min($productStock, $inventoryStock);

        if ($actualAvailable < $quantity) {
            throw new \Exception("Insufficient stock for {$product->name}. Requested: {$quantity}, Available: {$actualAvailable}");
        }

        // Reduce product stock
        $product->stock -= $quantity;
        $product->save();

        // Reduce inventory
        $this->reduceInventoryStock($productId, $quantity, $reason, $order);
    }

    /**
     * Restore both product stock and inventory atomically
     */
    private function restoreProductAndInventory(int $productId, int $quantity, string $reason, ?Order $order = null): void
    {
        $product = Product::findOrFail($productId);

        // Restore product stock
        $product->stock += $quantity;
        $product->save();

        // Restore inventory
        $this->restoreInventoryStock($productId, $quantity, $reason, $order);
    }

    /**
     * Reduce inventory stock with FIFO logic
     */
    protected function reduceInventoryStock(int $productId, int $quantity, string $reason, ?Order $order = null): void
    {
        $inventoryItems = Inventory::where('product_id', $productId)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->orderBy('expiration_date')
            ->orderBy('received_date')
            ->get();

        $remainingToReduce = $quantity;

        foreach ($inventoryItems as $inventory) {
            if ($remainingToReduce <= 0) break;

            $reduceAmount = min($inventory->quantity, $remainingToReduce);
            $remainingToReduce -= $reduceAmount;

            // Create adjustment record
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'decrease',
                'quantity_change' => -$reduceAmount,
                'reason' => $reason,
                'notes' => $order ? "Order: {$order->order_number}" : 'Stock reduction',
                'user_id' => Auth::id(),
                'user_name' => Auth::check() ? Auth::user()->name : 'System'
            ]);

            // Update inventory
            $inventory->quantity -= $reduceAmount;
            if ($inventory->quantity <= 0) {
                $inventory->status = 'depleted';
            }
            $inventory->save();
        }

        if ($remainingToReduce > 0) {
            throw new \Exception("Could not reduce full quantity from inventory for product ID {$productId}");
        }
    }

    /**
     * Restore inventory stock
     */
    private function restoreInventoryStock(int $productId, int $quantity, string $reason, ?Order $order = null): void
    {
        // Find the most recent inventory record or create new one
        $inventory = Inventory::where('product_id', $productId)
            ->where('status', 'available')
            ->latest()
            ->first();

        if (!$inventory) {
            // Create new inventory record if none exists
            $product = Product::findOrFail($productId);
            $inventory = Inventory::create([
                'product_id' => $productId,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit' => 'pcs',
                'batch_number' => 'REST-' . date('Y-m-d') . '-' . $productId,
                'status' => 'available',
                'received_date' => now(),
                'supplier_id' => $product->supplier_id,
                'location' => 'Main Warehouse',
                'reorder_level' => 10
            ]);
        } else {
            // Add to existing inventory
            $inventory->quantity += $quantity;
            if ($inventory->status === 'depleted') {
                $inventory->status = 'available';
            }
            $inventory->save();
        }

        // Create adjustment record
        InventoryAdjustment::create([
            'inventory_id' => $inventory->id,
            'adjustment_type' => 'increase',
            'quantity_change' => $quantity,
            'reason' => $reason,
            'notes' => $order ? "Order: {$order->order_number}" : 'Stock restoration',
            'user_id' => Auth::id(),
            'user_name' => Auth::check() ? Auth::user()->name : 'System'
        ]);
    }
}
