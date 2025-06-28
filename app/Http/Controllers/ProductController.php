<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();; 
        return view('products.index', compact('products'));

    }
    public function create()
{
      return view('products.create');
    }

    // Store a new product
    public function store(Request $request)
    {
        // Validate and save
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created!');
    }

    // Show a single product
    public function show($id){
    $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    // Show the form to edit a product
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        // $categories = Category::all();
        // return view('products.edit', compact('product', 'categories'));
        return view('products.edit', compact('product'));
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Product updated!');
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }




}




