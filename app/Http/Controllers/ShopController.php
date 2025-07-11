<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the shop homepage with products
     */
    public function index(Request $request)
    {
        // Get featured products if no search is active
        $featured = null;
        if (!$request->filled('search') && !$request->filled('category')) {
            $featured = Product::where('featured', true)
                ->where('stock', '>', 0)
                ->take(4)
                ->get();
        }

        // Build product query
        $query = Product::query();

        // Apply search if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ingredients', 'like', "%{$search}%");
            });
        }

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $products = $query->paginate(12);

        // Get unique categories for filter dropdown
        $categories = Product::distinct()->whereNotNull('category')->pluck('category');

        return view('shop.index', compact('products', 'featured', 'categories'));
    }

    /**
     * Display a single product
     */
    public function show($id)
    {
        $product = Product::with('supplier')->findOrFail($id);

        // Get reviews
        $reviews = ProductReview::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get related products (same category or from same supplier)
        $relatedProducts = Product::where('id', '!=', $id)
            ->where(function ($query) use ($product) {
                if ($product->category) {
                    $query->where('category', $product->category);
                }
                if ($product->supplier_id) {
                    $query->orWhere('supplier_id', $product->supplier_id);
                }
            })
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'reviews', 'relatedProducts'));
    }

    /**
     * Store a product review
     */
    public function storeReview(Request $request, $id)
    {
        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        ProductReview::create([
            'product_id' => $id,
            'reviewer_name' => $validated['reviewer_name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
}