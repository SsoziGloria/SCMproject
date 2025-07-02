<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
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


    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $suppliers = User::where('role', 'supplier')->get();
        $categories = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('products.create', compact('suppliers', 'categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'product_id' => 'required|unique:products,product_id|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $image->getClientOriginalExtension();

            // Resize and save image
            $resizedImage = Image::read($image)
                ->cover(500, 500)
                ->toJpeg(90);

            Storage::disk('public')->put('products/' . $filename, $resizedImage);
            $validated['image'] = 'products/' . $filename;
        }

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['supplier', 'inventories']);

        // Inventory batches for this product
        $inventoryBatches = $product->inventories()->latest()->get();

        // Recent orders containing this product
        $recentOrders = Order::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->with('user')->latest()->take(10)->get();

        // Total orders count for this product
        $totalOrdersCount = Order::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->count();

        // Total revenue for this product
        $totalRevenue = \App\Models\OrderItem::where('order_items.product_id', $product->id)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->sum(\DB::raw('order_items.price * order_items.quantity'));

        // Recent shipments for this product
        $recentShipments = \App\Models\Shipment::where('product_id', $product->id)
            ->latest()->take(10)->get();

        // Total shipments count for this product
        $totalShipmentsCount = \App\Models\Shipment::where('product_id', $product->id)->count();

        // Total inventory quantity (sum of all batches)
        $totalInventoryQuantity = $inventoryBatches->sum('quantity');

        // All suppliers (for the add inventory modal)
        $suppliers = \App\Models\User::where('role', 'supplier')->get();

        return view('products.product-details', compact(
            'product',
            'inventoryBatches',
            'recentOrders',
            'recentShipments',
            'totalOrdersCount',
            'totalRevenue',
            'totalShipmentsCount',
            'totalInventoryQuantity',
            'suppliers'
        ));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $suppliers = User::where('role', 'supplier')->get();
        $categories = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('products.edit', compact('product', 'suppliers', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'product_id' => 'required|max:100|unique:products,product_id,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $image->getClientOriginalExtension();

            // Resize and save image
            $resizedImage = Image::read($image)
                ->cover(500, 500)
                ->toJpeg(90);

            Storage::disk('public')->put('products/' . $filename, $resizedImage);
            $validated['image'] = 'products/' . $filename;
        }

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product can be deleted (no orders, etc)
        $hasOrders = Order::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->exists();

        if ($hasOrders) {
            return response()->json([
                'success' => false,
                'message' => 'Product cannot be deleted because it has associated orders.'
            ]);
        }

        // Delete product image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Adjust stock quantity
     */
    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $newStock = $product->stock + $validated['adjustment'];

        if ($newStock < 0) {
            return back()->with('error', 'Cannot adjust stock below zero.');
        }

        // Log the stock adjustment
        $product->inventories()->create([
            'quantity' => $validated['adjustment'],
            'reason' => $validated['reason'],
            'user_id' => auth()->id(),
        ]);

        $product->stock = $newStock;
        $product->save();

        return back()->with('success', 'Stock adjusted successfully.');
    }

    /**
     * Export products to Excel/CSV
     */
    public function export(Request $request)
    {
        $filters = $request->only(['category', 'supplier', 'stock', 'search']);
        return Excel::download(new ProductsExport($filters), 'products.xlsx');
    }

    /**
     * Bulk actions on products
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,feature,unfeature,category',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'category' => 'required_if:action,category|nullable|string|max:100',
        ]);

        $count = 0;

        switch ($validated['action']) {
            case 'delete':
                foreach ($validated['products'] as $id) {
                    $product = Product::find($id);

                    // Skip products with orders
                    $hasOrders = Order::whereHas('items', function ($query) use ($id) {
                        $query->where('product_id', $id);
                    })->exists();

                    if (!$hasOrders) {
                        if ($product->image && Storage::disk('public')->exists($product->image)) {
                            Storage::disk('public')->delete($product->image);
                        }
                        $product->delete();
                        $count++;
                    }
                }
                $message = "{$count} products deleted successfully.";
                break;

            case 'feature':
                Product::whereIn('id', $validated['products'])->update(['featured' => true]);
                $count = count($validated['products']);
                $message = "{$count} products marked as featured.";
                break;

            case 'unfeature':
                Product::whereIn('id', $validated['products'])->update(['featured' => false]);
                $count = count($validated['products']);
                $message = "{$count} products unmarked as featured.";
                break;

            case 'category':
                Product::whereIn('id', $validated['products'])->update(['category' => $validated['category']]);
                $count = count($validated['products']);
                $message = "{$count} products updated to category: {$validated['category']}.";
                break;
        }

        return back()->with('success', $message);
    }
}