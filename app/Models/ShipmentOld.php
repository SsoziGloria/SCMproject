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
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_number',
        'order_id',
        'shipment_type',
        'supplier_id',
        'product_id',
        'quantity',
        'status',
        'expected_delivery',
        'shipped_at',
        'delivered_at',
        'tracking_number',
        'carrier',
        'customer_address',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expected_delivery' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
    ];

    /**
     * Get the supplier associated with the shipment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the order associated with the shipment (for customer orders).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with the shipment.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for incoming shipments (from suppliers)
     */
    public function scopeIncoming($query)
    {
        return $query->where('shipment_type', 'incoming');
    }

    /**
     * Scope for outgoing shipments (to customers)
     */
    public function scopeOutgoing($query)
    {
        return $query->where('shipment_type', 'outgoing');
    }

    /**
     * Generate a unique shipment number.
     */
    public static function generateShipmentNumber(): string
    {
        $prefix = 'SHP';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
        $number = $prefix . '-' . $timestamp . '-' . $random;

        // Check if number already exists and regenerate if needed
        if (static::where('shipment_number', $number)->exists()) {
            return self::generateShipmentNumber();
        }

        return $number;
    }

    /**
     * Scope a query to only include shipments with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending shipments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include shipped shipments.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered shipments.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled shipments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include shipments that are overdue.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('expected_delivery')
            ->whereDate('expected_delivery', '<', now());
    }

    /**
     * Check if the shipment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending'
            && $this->expected_delivery
            && $this->expected_delivery->isPast();
    }

    /**
     * Check if the shipment can be marked as shipped.
     */
    public function canBeShipped(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the shipment can be marked as delivered.
     */
    public function canBeDelivered(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if the shipment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'shipped']);
    }
}
