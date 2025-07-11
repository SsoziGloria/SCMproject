<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'quantity',
        'unit',
        'batch_number',
        'status',
        'received_date',
        'supplier_id',
        'location',
        'expiration_date'
    ];

    protected $casts = [
        'received_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Get items that are low in stock (below threshold)
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('quantity', '<', $threshold);
    }

    // Get items that are expiring soon (within days)
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($days))
            ->whereDate('expiration_date', '>=', now());
    }

    // Get expired items
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now());
    }
}