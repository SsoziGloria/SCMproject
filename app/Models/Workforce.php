<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Worker;

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
        return $this->belongsTo(Worker ::class);
    }
}
