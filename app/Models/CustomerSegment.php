<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    protected $table = 'customer_segments';
    protected $fillable = ['customer_id', 'quantity', 'total_quantity', 'purchase_count', 'cluster'];

    // Disable updated_at since table only has created_at
    const UPDATED_AT = null;

    // Specify the created_at column name
    const CREATED_AT = 'created_at';
}
