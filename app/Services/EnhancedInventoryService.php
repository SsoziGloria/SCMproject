<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnhancedInventoryService extends InventoryService
{
    /**
     * Check if enough stock is available for order (considering both inventory and allocated stock)
     * This respects the two-phase system: allocation → shipment
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

            // Get the true available stock considering:
            // 1. Product stock minus allocated stock (prevents overselling)
            // 2. Actual inventory available (ensures physical stock exists)
            $productAvailable = $product->stock - $product->allocated_stock;
            $inventoryAvailable = $this->getAvailableInventoryStock($product->id);

            // The actual available is the minimum of both
            $actualAvailable = min($productAvailable, $inventoryAvailable);

            if ($actualAvailable < $item['quantity']) {
                $issues[] = "Not enough stock for {$product->name}. Requested: {$item['quantity']}, Available: {$actualAvailable} (Product: {$productAvailable}, Inventory: {$inventoryAvailable})";
            }
        }

        return $issues;
    }

    /**
     * Reserve stock for an order (Phase 1: Allocation)
     * This increases allocated_stock but doesn't touch inventory yet
     */
    public function reserveStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);

                // Verify stock is still available
                $available = $product->stock - $product->allocated_stock;
                if ($available < $item->quantity) {
                    throw new \Exception("Insufficient available stock for {$product->name}. Available: {$available}, Requested: {$item->quantity}");
                }

                // Reserve the stock
                $product->allocated_stock += $item->quantity;
                $product->save();

                // Log the allocation (optional - for audit trail)
                \Log::info("Reserved {$item->quantity} units of {$product->name} for order {$order->order_number}");
            }
        });
    }

    /**
     * Release reserved stock when order is cancelled (before shipment)
     */
    public function releaseReservedStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);

                // Only release if not yet shipped
                $unshippedQuantity = $item->quantity - ($item->quantity_shipped ?? 0);
                if ($unshippedQuantity > 0) {
                    $product->allocated_stock -= $unshippedQuantity;
                    $product->save();

                    \Log::info("Released {$unshippedQuantity} units of {$product->name} from cancelled order {$order->order_number}");
                }
            }
        });
    }

    /**
     * Ship order items (Phase 2: Actually reduce inventory and release allocation)
     * This is called when order status changes to 'shipped'
     */
    public function shipOrderItems(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);

                // Calculate how much needs to be shipped
                $neededQuantity = $item->quantity - ($item->quantity_shipped ?? 0);
                if ($neededQuantity <= 0) continue;

                // Verify inventory is available
                $inventoryAvailable = $this->getAvailableInventoryStock($product->id);
                if ($inventoryAvailable < $neededQuantity) {
                    throw new \Exception("Insufficient inventory for {$product->name}. Available: {$inventoryAvailable}, Needed: {$neededQuantity}");
                }

                // Reduce actual inventory (FIFO)
                $this->reduceInventoryStock(
                    $product->id,
                    $neededQuantity,
                    "Order shipment: {$order->order_number}",
                    $order
                );

                // Release the allocated stock
                $product->allocated_stock -= $neededQuantity;
                $product->save();

                // Update order item
                $item->quantity_shipped = $item->quantity;
                $item->save();

                \Log::info("Shipped {$neededQuantity} units of {$product->name} for order {$order->order_number}");
            }
        });
    }

    /**
     * Get truly available stock considering both product stock and inventory
     */
    public function getTrueAvailableStock(int $productId): int
    {
        $product = Product::findOrFail($productId);
        $productAvailable = $product->stock - $product->allocated_stock;
        $inventoryAvailable = $this->getAvailableInventoryStock($productId);

        return min($productAvailable, $inventoryAvailable);
    }

    /**
     * Sync allocated stock with actual order items (maintenance function)
     */
    public function syncAllocatedStock(): array
    {
        $results = [];
        $products = Product::all();

        foreach ($products as $product) {
            // Calculate what allocated stock should be based on pending orders
            $actualAllocated = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.product_id', $product->id)
                ->whereIn('orders.status', ['pending', 'processing'])
                ->sum(DB::raw('order_items.quantity - COALESCE(order_items.quantity_shipped, 0)'));

            if ($product->allocated_stock != $actualAllocated) {
                $results[] = [
                    'product' => $product->name,
                    'current_allocated' => $product->allocated_stock,
                    'should_be_allocated' => $actualAllocated,
                    'difference' => $actualAllocated - $product->allocated_stock
                ];

                // Fix the allocation
                $product->allocated_stock = $actualAllocated;
                $product->save();
            }
        }

        return $results;
    }

    /**
     * Get detailed stock status for a product
     */
    public function getProductStockStatus(int $productId): array
    {
        $product = Product::findOrFail($productId);
        $inventoryStock = $this->getAvailableInventoryStock($productId);

        return [
            'product_id' => $productId,
            'product_name' => $product->name,
            'product_stock' => $product->stock,
            'allocated_stock' => $product->allocated_stock,
            'available_from_product' => $product->stock - $product->allocated_stock,
            'inventory_stock' => $inventoryStock,
            'true_available' => min($product->stock - $product->allocated_stock, $inventoryStock),
            'in_sync' => $product->stock == $inventoryStock,
            'allocation_ratio' => $product->stock > 0 ? round(($product->allocated_stock / $product->stock) * 100, 1) : 0
        ];
    }
}
