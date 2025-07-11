<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandPrediction extends Model
{
    protected $table = 'demand_predictions';
    protected $fillable = ['product_id', 'prediction_date', 'predicted_quantity'];
}
