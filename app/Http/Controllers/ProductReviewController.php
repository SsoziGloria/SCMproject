<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = productReview::with('product')->get();
        return view('productReviews.index', compact('reviews'));
    }

    public function create(Request $request)
    {
        $products = Product::all();
        $preselectedProductId = $request->query('product_id');
        return view('productReviews.create', compact('products', 'preselectedProductId'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        ProductReview::create($validated);

        $prod_id = $validated['product_id'];
        if (Auth::user()->role !== 'user')
            return redirect()->route('productReviews.index')->with('success', 'Review added!');
        else
            return redirect()->route('productReviews.show', $prod_id)->with('success', 'Thank you for your review!');
    }

    public function edit($id)
    {
        $review = ProductReview::findOrFail($id);
        $products = Product::all();
        return view('productReviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return redirect()->route('productReviews.index')->with('success', 'Review updated successfully!');
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();

        return redirect()->route('productReviews.index')->with('success', 'Review deleted successfully!');
    }

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
}