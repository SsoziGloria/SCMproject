<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'shipment_number',
        'order_id',
        'supplier_id',
        'product_id',
        'quantity',
        'status',
        'expected_delivery',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expected_delivery' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            if (empty($shipment->shipment_number)) {
                $shipment->shipment_number = 'SH-' . str_pad(static::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the order associated with the shipment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the supplier associated with the shipment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the product associated with the shipment.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'processing' => 'warning',
            'shipped' => 'info',
            'in_transit' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'processing' => 'hourglass-split',
            'shipped' => 'box-seam',
            'in_transit' => 'truck',
            'delivered' => 'check-circle',
            'cancelled' => 'x-circle',
            default => 'question-circle'
        };
    }

    /**
     * Check if shipment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->expected_delivery &&
            $this->expected_delivery->isPast() &&
            $this->status !== 'delivered';
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        return match ($this->status) {
            'processing' => 25,
            'shipped' => 50,
            'in_transit' => 75,
            'delivered' => 100,
            'cancelled' => 0,
            default => 0
        };
    }

    /**
     * Scope for order shipments
     */
    public function scopeForOrders($query)
    {
        return $query->whereNotNull('order_id');
    }

    /**
     * Scope for supplier shipments
     */
    public function scopeForSuppliers($query)
    {
        return $query->whereNotNull('supplier_id')->whereNull('order_id');
    }

    /**
     * Update status with automatic timestamps
     */
    public function updateStatus(string $status): bool
    {
        $this->status = $status;

        if ($status === 'shipped' && !$this->shipped_at) {
            $this->shipped_at = now();
        }

        if ($status === 'delivered' && !$this->delivered_at) {
            $this->delivered_at = now();

            // Handle order shipments
            if ($this->order_id && $this->order) {
                $this->order->status = 'delivered';
                $this->order->delivered_at = now();
                $this->order->save();

                // Create order status history
                \App\Models\OrderStatusHistory::create([
                    'order_id' => $this->order_id,
                    'status' => 'delivered',
                    'user_id' => Auth::id() ?: 1, // Default to admin if no user
                    'notes' => "Order automatically marked as delivered via shipment #{$this->shipment_number}"
                ]);
            }

            // Handle supplier delivery shipments - increase inventory
            if ($this->supplier_id && !$this->order_id && $this->product_id && $this->quantity > 0) {
                DB::beginTransaction();

                try {
                    // Find existing inventory or create new one
                    $inventory = Inventory::where('product_id', $this->product_id)
                        ->where('supplier_id', $this->supplier_id)
                        ->where('status', 'available')
                        ->first();

                    if (!$inventory) {
                        // Create new inventory record
                        $inventory = Inventory::create([
                            'product_id' => $this->product_id,
                            'product_name' => $this->product->name ?? "Product #{$this->product_id}",
                            'quantity' => 0,
                            'unit' => 'pcs', // Default unit, could be made configurable
                            'status' => 'available',
                            'supplier_id' => $this->supplier_id,
                            'location' => 'Warehouse', // Default location
                            'received_date' => now(),
                            'batch_number' => "BATCH-" . $this->shipment_number,
                        ]);
                    }

                    // Create inventory adjustment record
                    \App\Models\InventoryAdjustment::create([
                        'inventory_id' => $inventory->id,
                        'adjustment_type' => 'increase',
                        'quantity_change' => $this->quantity,
                        'reason' => "Supplier delivery received: {$this->shipment_number}",
                        'notes' => "Automatic adjustment for supplier delivery from " . ($this->supplier->name ?? 'Unknown Supplier'),
                        'user_id' => Auth::id() ?: 1,
                        'user_name' => Auth::user()->name ?? 'System',
                    ]);

                    // Update inventory quantity
                    $inventory->quantity += $this->quantity;
                    $inventory->received_date = now();
                    $inventory->save();

                    // Update product stock if product exists
                    if ($this->product) {
                        $this->product->stock += $this->quantity;
                        $this->product->save();
                    }

                    DB::commit();

                    Log::info("Supplier delivery processed: Shipment #{$this->shipment_number}, added {$this->quantity} units to inventory");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to process supplier delivery for shipment #{$this->shipment_number}: " . $e->getMessage());
                    throw $e;
                }
            }
        }

        return $this->save();
    }
}
