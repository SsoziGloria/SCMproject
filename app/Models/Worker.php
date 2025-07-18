<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Workforce;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
    ];


    public function workforce()
{
    return $this->hasMany(Workforce::class);
}

    public function assignments()
    {
        return $this->hasMany('App\Models\Workforce');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($worker) {
            $worker->assignments()->delete();
        });
    }
}
