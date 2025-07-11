<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReviews;
use App\Models\Product;

class ProductReviewController extends Controller
{
public function index()
    {
        $reviews = productReviews::with('product')->get();
        return view('productReviews.index', compact('reviews'));
    } 

    public function create()
{
    $products = Product::all();
    return view('productReviews.create', compact('products'));
}

public function store(Request $request){
    
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'reviewer_name' => 'required|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string',
    ]);

    ProductReviews::create($validated);

    return redirect()->route('productReviews.index')->with('success', 'Review added!');
}
}
