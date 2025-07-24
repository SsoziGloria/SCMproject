<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'order_number',
        'user_id',
        'email',
        'phone',
        'address',
        'shipping_address',
        'shipping_city',
        'shipping_region',
        'shipping_country',
        'total_amount',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'status',
        'payment_status',
        'payment',
        'notes',
        'retailer_id',
        'sales_channel',
        'sales_channel_id',
        'referral_source',
        'tracking_number'
    ];
    protected $casts = [
        'ordered_at' => 'date',
        'delivered_at' => 'date',
        'order_date' => 'datetime',
        'total_amount' => 'decimal:0'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'product_id',);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function supplier()
    {
        return $this->belongsTo(\App\Models\User::class, 'supplier_id');
    }

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

    /**
     * Get the shipments for this order
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get the retailer that processed the order.
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    /**
     * Get the sales channel for this order.
     */
    public function salesChannel()
    {
        return $this->belongsTo(SalesChannel::class);
    }

    /**
     * Get the promotions applied to this order.
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'order_promotions')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
     * Calculate profit for this order.
     */
    public function getProfit()
    {
        $profit = 0;
        foreach ($this->items as $item) {
            $profit += ($item->price - ($item->unit_cost ?? 0)) * $item->quantity;
        }
        return $profit;
    }

    /**
     * Calculate profit margin for this order.
     */
    public function getProfitMargin()
    {
        if ($this->subtotal > 0) {
            return ($this->getProfit() / $this->subtotal) * 100;
        }
        return 0;
    }

    /**
     * Get number of items in the order.
     */
    public function getItemCount()
    {
        return $this->items->sum('quantity');
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
            ->withPivot('quantity', 'price');
    }

    /**
     * Mark order as shipped and create shipment record
     */
    public function markAsShipped($expectedDelivery = null): bool
    {
        $this->status = 'shipped';
        $saved = $this->save();

        if ($saved) {
            // Create shipment record
            Shipment::create([
                'order_id' => $this->id,
                'shipment_number' => 'SH-' . str_pad(Shipment::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'status' => 'shipped',
                'expected_delivery' => $expectedDelivery ?: now()->addDays(3),
                'shipped_at' => now(),
                'notes' => "Shipment created for order #{$this->order_number}"
            ]);
        }

        return $saved;
    }

    /**
     * Format total amount in UGX
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'UGX ' . number_format($this->total_amount, 0);
    }

    /**
     * Format price in UGX
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'UGX ' . number_format($this->price, 0);
    }

    /**
     * Get customer name from user relationship
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Unknown Customer';
    }

    /**
     * Get customer email from user relationship
     */
    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'payment_status', 'shipping_address'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order #{$this->order_number} has been {$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
