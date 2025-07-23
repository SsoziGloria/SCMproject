<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'required_workers',
        'location',
        'priority',
        'is_active',
        'status_for_day',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(Workforces::class, 'task', 'name');
    }
}
