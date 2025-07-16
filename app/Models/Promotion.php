<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the orders that used this promotion.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_promotions')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
     * Check if the promotion is currently valid.
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active &&
            $now->greaterThanOrEqualTo($this->start_date) &&
            $now->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount($orderTotal)
    {
        if ($this->discount_type === 'percentage') {
            return ($orderTotal * $this->discount_value) / 100;
        }

        return min($this->discount_value, $orderTotal);
    }
}