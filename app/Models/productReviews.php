<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductReviews extends Model
{
    protected $fillable = [
        'product_id',
        'reviewer_name',
        'rating',
        'comment',
    ];
    

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
