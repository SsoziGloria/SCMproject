<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'date_from',
        'date_to',
        'format',
        'status',
        'generated_by',
        'data',
        'file_path',
        'email_recipients',
        'schedule_frequency'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
