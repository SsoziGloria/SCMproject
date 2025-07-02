<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // Order item belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Order item is for a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate subtotal
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }
}