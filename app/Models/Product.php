<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function supplier()
    {
        return $this->belongsTo(\App\Models\User::class, 'supplier_id');
    }
    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'product_id');
    }
}
