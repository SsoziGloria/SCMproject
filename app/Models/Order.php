<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'total_amount',
        'status',
        'payment',
        'payment_status',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'notes',
        'ordered_at',
        'delivered_at',
        'order_date'
    ];

    protected $casts = [
        'ordered_at' => 'date',
        'delivered_at' => 'date',
        'order_date' => 'datetime',
        'total_amount' => 'decimal:0'
    ];

    /**
     * Boot the model and automatically generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber()
    {
        do {
            // Generate order number like: ORD-2024-001234
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Alternative: Sequential order numbers
     */
    public static function generateSequentialOrderNumber()
    {
        $lastOrder = self::latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;

        return 'ORD-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for this order
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope for filtering orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering orders by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the items for this order
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}