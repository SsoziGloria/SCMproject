<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;


class UpdateInventoryForOrderStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        // Handle inventory changes based on status change
        DB::beginTransaction();
        try {
            if (in_array($newStatus, ['shipped', 'delivered']) && !in_array($oldStatus, ['shipped', 'delivered'])) {
                $this->reduceInventory($order);
            } elseif ($newStatus === 'cancelled' && in_array($oldStatus, ['shipped', 'delivered'])) {
                $this->restoreInventory($order);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update inventory for order: ' . $e->getMessage());
        }
    }

    private function reduceInventory(Order $order)
    {
        foreach ($order->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)
                ->where('status', 'available')
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc') // Use FIFO (First In, First Out)
                ->first();

            if (!$inventory) {
                throw new \Exception("Insufficient inventory for product: {$item->product->name}");
            }

            // Record inventory adjustment
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'decrease',
                'quantity_change' => -$item->quantity,
                'reason' => "Order shipment: {$order->order_number}",
                'notes' => "Automatic adjustment for order: {$order->order_number}",
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
            ]);

            // Update inventory quantity
            $inventory->quantity -= $item->quantity;

            // If quantity becomes zero, update status
            if ($inventory->quantity <= 0) {
                $inventory->status = 'depleted';
            }

            $inventory->save();
        }
    }

    private function restoreInventory(Order $order)
    {
        foreach ($order->items as $item) {
            // Find the original inventory item or create one if needed
            $inventory = Inventory::where('product_id', $item->product_id)
                ->where(function ($query) {
                    $query->where('status', 'available')
                        ->orWhere('status', 'depleted');
                })
                ->orderBy('updated_at', 'desc')
                ->first();

            if (!$inventory) {
                // Create new inventory record if none exists
                $product = Product::findOrFail($item->product_id);
                $inventory = Inventory::create([
                    'product_id' => $item->product_id,
                    'product_name' => $product->name,
                    'quantity' => 0,
                    'status' => 'available',
                    'location' => 'Returned stock',
                ]);
            }

            // Record inventory adjustment
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'increase',
                'quantity_change' => $item->quantity,
                'reason' => "Order cancelled: {$order->order_number}",
                'notes' => "Automatic adjustment - stock restored for cancelled order",
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
            ]);

            // Update inventory
            $inventory->quantity += $item->quantity;

            // If inventory was depleted, mark as available again
            if ($inventory->status === 'depleted') {
                $inventory->status = 'available';
            }

            $inventory->save();
        }
    }
}
