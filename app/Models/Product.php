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

    protected function availableStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->stock - $this->allocated_stock,
        );
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
