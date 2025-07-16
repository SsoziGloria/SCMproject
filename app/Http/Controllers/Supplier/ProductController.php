<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'supplier') {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Product::with('category')->where('supplier_id', Auth::id());

        // Handle search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('supplier.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('supplier.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|max:50|unique:products',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
        ]);

        // Always set the supplier_id to the currently logged-in supplier
        $validated['supplier_id'] = Auth::id();

        // Handle featured checkbox
        $validated['featured'] = $request->has('featured') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid('product_') . '.' . $image->getClientOriginalExtension();

            $resized = Image::read($image)->toJpeg(90);
            Storage::disk('public')->put('product/' . $filename, $resized);

            $validated['image'] = 'product/' . $filename;
        }

        Product::create($validated);

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('supplier.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|string|max:50|unique:products,product_id,' . $product->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
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

            $image = $request->file('image');
            $filename = uniqid('product_') . '.' . $image->getClientOriginalExtension();

            $resized = Image::read($image)->toJpeg(90);
            Storage::disk('public')->put('product/' . $filename, $resized);

            $validated['image'] = 'product/' . $filename;
        }

        // Handle image removal checkbox
        if ($request->has('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);

        // Delete the product image if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}