<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'name',
        'ingredients',
        'price',
        'description',
        'image',
        'featured',
        'stock',
        'category',
        'supplier_id',
    ];
<<<<<<< Updated upstream

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'stock' => 'integer',
    ];
=======
<<<<<<< HEAD
=======
>>>>>>> Stashed changes
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\User::class, 'supplier_id');
    }
    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'product_id');
    }
<<<<<<< Updated upstream

    public function isLowStock()
    {
        return $this->stock <= 5;
    }

    /**
     * Check if the product is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/placeholder-image.png');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
=======
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
>>>>>>> Stashed changes
}
