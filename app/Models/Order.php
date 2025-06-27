<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'status',
        'order_date',
    ];


    public function inventory() {
    return $this->belongsTo(Inventory::class, 'product_id',);
}
public function user() {
    return $this->belongsTo(User::class);
}

}
