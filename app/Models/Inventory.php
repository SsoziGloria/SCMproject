<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['product_id', 'product_name','quantity', 'location','expiration_date'];
    
}
