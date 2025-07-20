<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'adjustment_type',
        'quantity_change',
        'reason',
        'notes',
        'user_id',
        'user_name',
        'status_history_id'
    ];

    /**
     * Get the inventory record associated with the adjustment.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the user who performed the adjustment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistory()
    {
        return $this->belongsTo(OrderStatusHistory::class, 'status_history_id');
    }
}