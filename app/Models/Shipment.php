<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'status',
        'shipped_date',
        'estimated_delivery',
        'delivered_date',
        'notes',
    ];

    protected $casts = [
        'shipped_date' => 'date',
        'estimated_delivery' => 'date',
        'delivered_date' => 'date',
    ];

    // Shipment belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'pending' => 'Pending',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'returned' => 'Returned',
            'failed' => 'Failed Delivery'
        ];
    }

    // Carrier options
    public static function carrierOptions()
    {
        return [
            'fedex' => 'FedEx',
            'ups' => 'UPS',
            'usps' => 'USPS',
            'dhl' => 'DHL',
            'other' => 'Other'
        ];
    }
}