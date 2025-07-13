<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workforce extends Model
{
    
    protected $fillable = [
        'worker_id',
        'location',
        'task',
        'assigned_date',
        'created_at',
        'updated_at',
    ];

    public function worker()
    {
        return $this->belongsTo('App\Models\Worker', 'worker_id');
    }
}
