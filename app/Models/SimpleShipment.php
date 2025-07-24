<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        }

        return $this->save();
    }
}
