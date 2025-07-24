<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'name',
        'ingredients',
        'price',
        'description',
        'image',
        'featured',
        'stock',
        'category',
        'supplier_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Mutator to ensure allocated_stock never goes negative
     */
    public function setAllocatedStockAttribute($value)
    {
        $this->attributes['allocated_stock'] = max(0, (int)$value);
    }

    /**
     * Flag to temporarily disable observer sync during order processing
     */
    public static $skipObserverSync = false;

    /**
     * Update stock without triggering observer sync (for order processing)
     */
    public function updateStockSilently($newStock, $newAllocatedStock = null)
    {
        self::$skipObserverSync = true;

        $this->stock = $newStock;
        if ($newAllocatedStock !== null) {
            // Ensure allocated_stock never goes negative
            $this->allocated_stock = max(0, $newAllocatedStock);
        }
        $this->save();

        self::$skipObserverSync = false;
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'product_id');
    }

    public function isLowStock()
    {
        return $this->stock <= 5;
    }

    /**
     * Check if the product is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/placeholder-image.png');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot('quantity', 'price');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function availableStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->stock - $this->allocated_stock,
        );
    }

    /**
     * Get total inventory quantity across all available locations
     */
    public function getTotalInventoryQuantity()
    {
        return $this->inventories()
            ->where('status', 'available')
            ->sum('quantity');
    }

    /**
     * Check if product stock is synchronized with inventory
     */
    public function isStockSynced()
    {
        return $this->stock == $this->getTotalInventoryQuantity();
    }

    /**
     * Sync product stock to match total inventory quantity
     */
    public function syncStockToInventory()
    {
        $totalInventory = $this->getTotalInventoryQuantity();
        if ($this->stock != $totalInventory) {
            $this->updateStockSilently($totalInventory, $this->allocated_stock);
            return true;
        }
        return false;
    }

    /**
     * Format price in UGX
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'UGX ' . number_format($this->price, 0);
    }

    /**
     * Format cost in UGX
     */
    public function getFormattedCostAttribute(): string
    {
        return 'UGX ' . number_format($this->cost ?? 0, 0);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'stock'])
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
