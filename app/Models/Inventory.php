<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    
public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
    protected $fillable = ['product_id', 'product_name','quantity', 'location','expiration_date'];
    
}
