<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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

    protected $casts = [
        'featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Product belongs to a supplier (User with supplier role)
    public function supplier()
    {
        return $this->belongsTo(\App\Models\User::class, 'supplier_id');
    }

    // Product has many inventory adjustments
    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'product_id');
    }

    // Product can be in many orders (through OrderItem)
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    // Get all order items for this product
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope for low stock
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock', '<=', $threshold);
    }

    // Format price as currency
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}