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
        return $this->hasMany(Product::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get supplier name by supplier_id
     * 
     * @return string|null
     */
    public function getName()
    {
        if (!$this->supplier_id) {
            return null;
        }

        return $this->name;
    }

    /**
     * Get supplier name by supplier_id statically
     * 
     * @param int $supplier_id
     * @return string|null
     */
    public static function getNameById($supplier_id)
    {
        if (!$supplier_id) {
            return null;
        }

        return self::where('supplier_id', $supplier_id)->value('name');
    }
}
