<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Worker;

class Workforces extends Model
{

    protected $fillable = [
        'worker_id',
        'location',
        'task',
        'status',
        'completed_at',
        'assigned_date',
        'created_at',
        'updated_at',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
