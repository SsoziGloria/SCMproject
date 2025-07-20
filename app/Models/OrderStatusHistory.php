<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'notes',
        'user_id',
        'user_name',
    ];

    /**
     * Get the order that owns the status history.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that created the status update.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the inventory adjustments associated with this status change.
     */
    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'status_history_id');
    }

    /**
     * Get the status color for the timeline.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the status icon for the timeline.
     */
    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            'pending' => 'clock',
            'processing' => 'gear',
            'shipped' => 'truck',
            'delivered' => 'check-circle',
            'cancelled' => 'x-circle',
            default => 'question-circle'
        };
    }
}