<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    protected $table = 'customer_segments';
    protected $fillable = ['customer_id', 'quantity', 'total_quantity', 'purchase_count', 'cluster'];
}
