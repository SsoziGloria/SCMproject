<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'inventories']);

        // Apply filters
        if ($request->category)
            $query->where('category', $request->category);
        if ($request->supplier)
            $query->where('supplier_id', $request->supplier);
        if ($request->stock === 'low-stock')
            $query->where('stock', '<=', 10);

        $products = $query->paginate(20);

        return view('products.all-products', [
            'products' => $products,
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('stock', '>', 0)->count(),
            'lowStockCount' => Product::where('stock', '<=', 10)->count(),
            'categoriesCount' => Product::whereNotNull('category')->distinct('category')->count(),
            'totalInventoryValue' => Product::sum(DB::raw('price * stock')),
            'categories' => Product::whereNotNull('category')->distinct()->pluck('category'),
            'suppliers' => User::where('role', 'supplier')->get()
        ]);
    }

    //public function index()
    //{
    //    $products = Product::with('category')->get();; 
    //    return view('products.index', compact('products'));
    //}
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

    // Show a single product.
    public function show($id)
    {
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