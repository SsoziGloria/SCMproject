<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandPrediction extends Model
{
    protected $table = 'demand_predictions';
    protected $fillable = ['product_id', 'prediction_date', 'predicted_quantity'];

    // Disable timestamps since table doesn't have created_at/updated_at
    public $timestamps = false;

    /**
     * Get the product associated with this prediction
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
