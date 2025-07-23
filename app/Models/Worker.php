<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Workforces;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'status',
    ];


    public function workforce()
    {
        return $this->hasMany(Workforces::class);
    }

    public function assignments()
    {
        return $this->hasMany('App\Models\Workforces');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($worker) {
            $worker->assignments()->delete();
        });
    }
}
