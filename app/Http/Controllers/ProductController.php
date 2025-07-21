<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Supplier;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'category']);


        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->input('search');
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('product_id', 'like', "%{$search}%");
            });
        });

        $query->when($request->filled('category'), function ($q) use ($request) {
            return $q->where('category', $request->category);
        });

        $query->when($request->filled('supplier'), function ($q) use ($request) {
            return $q->where('supplier_id', $request->supplier);
        });

        $query->when($request->filled('stock'), function ($q) use ($request) {
            if ($request->stock === 'in-stock') {
                return $q->where('stock', '>', 10);
            }
            if ($request->stock === 'low-stock') {
                return $q->where('stock', '>', 0)->where('stock', '<=', 10);
            }
            if ($request->stock === 'out-of-stock') {
                return $q->where('stock', '=', 0);
            }
        });

        $products = $query->paginate(20)->withQueryString();

        return view('products.all-products', [
            'products' => $products,
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('stock', '>', 0)->count(),
            'lowStockCount' => Product::where('stock', '<=', 10)->count(),
            'categoriesCount' => Category::has('products')->count(),
            'totalInventoryValue' => Product::sum(DB::raw('price * stock')),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get()
        ]);
    }

    //public function index()
    //{
    //    $products = Product::with('category')->get();; 
    //    return view('products.index', compact('products'));
    //}

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('products', 'suppliers', 'categories'));
    }

    // Store a new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|max:50|unique:products',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
        ]);

        // Handle featured checkbox
        $validated['featured'] = $request->has('featured') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid('product_') . ' .jpg';

            $resized = Image::make($image)
                ->encode('jpg', 90);

            Storage::disk('public')->put('product/' . $filename, $resized);

            $validated['image'] = 'product/' . $filename;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('products.show', compact('product'));
    }

    // Show the form to edit a product
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        if (auth()->user()->role === 'admin') {
            $suppliers = Vendor::orderBy('name')->get();
        } else {
            $suppliers = Vendor::where('supplier_id', auth()->user()->id)->orderBy('name')->get();
        }
        $categories = Category::orderBy('name')->get();
        // return view('products.edit', compact('product', 'categories'));
        return view('products.edit', compact('product', 'suppliers', 'categories'));
    }

    // Update a product
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|max:50|unique:products,product_id,' . $product->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
            'featured' => 'nullable|boolean',
        ]);

        // Handle featured checkbox
        $validated['featured'] = $request->has('featured') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle image removal checkbox
        if ($request->has('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Delete a product
    public function destroy(Product $product)
    {
        // Delete the product image if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $productIds = $request->input('product_ids', []);

        if ($action === 'featured') {
            // Toggle featured for each selected product
            $products = Product::whereIn('id', $productIds)->get();
            foreach ($products as $product) {
                $product->featured = !$product->featured;
                $product->save();
            }
            return response()->json(['success' => true]);
        }

        // ... handle other actions ...

        return response()->json(['success' => false, 'message' => 'Invalid action.']);
    }


    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->stock = $request->stock;
        $product->save();

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->featured = !$product->featured;
        $product->save();

        return redirect()->back()->with('success', 'Featured status updated.');
    }
}
