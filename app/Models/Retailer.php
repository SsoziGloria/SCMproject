<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_address',
        'city',
        'region',
        'phone',
        'email',
        'tax_id',
        'status'
    ];

    /**
     * Get the user that owns the retailer account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all orders for this retailer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the retailer's active status
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}