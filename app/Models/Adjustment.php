<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Adjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'type',       
        'amount',
        'reason',
    ];

    // Relationship to Inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
