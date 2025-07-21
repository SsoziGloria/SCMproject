<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the products for the supplier
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
