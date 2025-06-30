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
}