<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function supplier()
    {
        return $this->belongsTo(\App\Models\User::class, 'supplier_id');
    }
}
