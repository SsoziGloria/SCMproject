<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class ShopController extends Controller
{
    /**
     * Display the shop homepage with products
     */
    public function index(Request $request)
    {
        $baseQuery = $this->getVisibleProductsQuery();


        $featured = null;
        if (!$request->anyFilled(['search', 'category', 'sort']) && !$request->has('page')) {
            $featured = (clone $baseQuery)
                ->where('featured', true)
                ->where('stock', '>', 0)
                ->take(4)
                ->get();
        }

        $productsQuery = (clone $baseQuery);

        $productsQuery->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            return $q->where(fn($subQ) => $subQ->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        });

        $productsQuery->when($request->filled('category'), fn($q) => $q->where('category', $request->category));

        $this->applySorting($productsQuery, $request->input('sort', 'default'));

        $products = $productsQuery->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->pluck('name', 'id');
        $suppliers = Supplier::whereHas('user', fn($q) => $q->where('is_active', true))->orderBy('name')->get();

        return view('shop.index', compact('products', 'featured', 'categories', 'suppliers'));
    }

    /**
     * Display a single product
     */
    public function show($id)
    {
        $product = $this->getVisibleProductsQuery()->with('supplier', 'category')->findOrFail($id);

        $reviews = ProductReview::where('product_id', $id)->latest()->get();

        $relatedProducts = $this->getVisibleProductsQuery()
            ->where('id', '!=', $id)
            ->where('category', $product->category)
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

    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getVisibleProductsQuery(): Builder
    {
        $showAllSupplierProducts = (bool) Setting::get('show_supplier_products', true);

        if (!$showAllSupplierProducts) {
            return Product::query()->whereNull('supplier_id');
        }

        $visibleSuppliers = Supplier::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->get();

        $visibleSupplierIds = $visibleSuppliers->filter(function ($supplier) {
            return Setting::get('show_supplier_products', true, $supplier->id);
        })->pluck('supplier_id');

        return Product::query()->where(function ($query) use ($visibleSupplierIds) {
            $query->whereNull('supplier_id')
                ->orWhereIn('supplier_id', $visibleSupplierIds);
        });
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sortOption
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applySorting(Builder $query, string $sortOption): Builder
    {
        switch ($sortOption) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'name_asc':
                return $query->orderBy('name', 'asc');
            case 'name_desc':
                return $query->orderBy('name', 'desc');
            default:
                return $query->latest();
        }
    }
}
