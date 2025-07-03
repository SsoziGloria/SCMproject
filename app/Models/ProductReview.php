<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_review';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'reviewer_name',
        'rating',
        'comment'
    ];

    /**
     * Get the product that owns the review.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the average rating for a product
     *
     * @param int $productId
     * @return float
     */
    public static function getAverageRating($productId)
    {
        return self::where('product_id', $productId)->avg('rating') ?: 0;
    }

    /**
     * Get rating distribution for a product (how many 1-stars, 2-stars, etc.)
     *
     * @param int $productId
     * @return array
     */
    public static function getRatingDistribution($productId)
    {
        $distribution = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        $counts = self::where('product_id', $productId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        foreach ($counts as $rating => $count) {
            $distribution[$rating] = $count;
        }

        return $distribution;
    }
}