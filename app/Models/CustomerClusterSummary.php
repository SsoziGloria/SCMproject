<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerClusterSummary extends Model
{
    protected $table = 'customer_cluster_summary';

     protected $fillable = [
        'cluster',
        'description',
        'customer_count',
        'product_types',
        'recommendation_strategy',
        'created_at'
    ];
}
