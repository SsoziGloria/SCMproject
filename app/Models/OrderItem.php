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
        'product_name',
        'product_category',
        'quantity',
        'price',
        'unit_cost',
        'discount_amount',
        'subtotal'
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that the item references.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the line item total.
     */
    public function getTotal()
    {
        return ($this->price * $this->quantity) - $this->discount_amount;
    }

    /**
     * Get the profit for this line item.
     */
    public function getProfit()
    {
        return ($this->price - ($this->unit_cost ?? 0)) * $this->quantity;
    }
}