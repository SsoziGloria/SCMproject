<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shipment;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;

class InventoryController extends Controller
{
    /**
     * Display a paginated listing of the inventory.
     */


    public function index(Request $request)
    {
        // Authorization check
        \Illuminate\Support\Facades\Auth::user()->can('viewAny', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        // Basic stats for inventory dashboard
        $stats = [
            'total_products' => Product::count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)
                ->where('stock', '<=', 10)
                ->count(),
            'total_value' => Product::sum(DB::raw('price * stock')),
            'expiring_soon' => Inventory::whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now())
                ->where('expiry_date', '<=', now()->addMonths(3))
                ->count(),
        ];

        // Filter inventory records
        $query = Inventory::with(['product', 'user']);

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('type')) {
            if ($request->type === 'addition') {
                $query->where('quantity', '>', 0);
            } elseif ($request->type === 'reduction') {
                $query->where('quantity', '<', 0);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch')) {
            $query->where('batch_number', 'like', '%' . $request->batch . '%');
        }

        // Sort
        $sortField = $request->filled('sort') ? $request->sort : 'created_at';
        $sortDirection = $request->filled('direction') ? $request->direction : 'desc';
        $query->orderBy($sortField, $sortDirection);

        $inventories = $query->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('inventory.index', compact(
            'inventories',
            'products',
            'stats'
        ));
    }

    /**
     * Show form to add new inventory
     */
    public function create()
    {
        \Illuminate\Support\Facades\Auth::user()->can('create', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        $products = Product::orderBy('name')->get();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store new inventory record and update product stock
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Auth::user()->can('create', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|not_in:0',
            'batch_number' => 'nullable|string|max:100',
            'manufactured_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufactured_date',
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $newStock = $product->stock + $validated['quantity'];

        // Prevent negative stock
        if ($newStock < 0) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'Cannot reduce stock below zero. Current stock: ' . $product->stock]);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create inventory record
            $inventory = Inventory::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'batch_number' => $validated['batch_number'],
                'manufactured_date' => $validated['manufactured_date'],
                'expiry_date' => $validated['expiry_date'],
                'reason' => $validated['reason'],
                'user_id' => auth()->id(),
            ]);

            // Update product stock
            $product->stock = $newStock;
            $product->save();

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Display inventory record
     */
    public function show(Inventory $inventory)
    {
        \Illuminate\Support\Facades\Auth::user()->can('view', $inventory)
            ?: abort(403, 'This action is unauthorized.');

        $inventory->load(['product', 'user']);
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show edit form
     */
    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        // Only allow editing very recent inventory entries
        $cutoffTime = Carbon::now()->subHours(24);
        if ($inventory->created_at->lt($cutoffTime)) {
            return redirect()->route('inventory.show', $inventory)
                ->with('error', 'Cannot edit inventory records older than 24 hours.');
        }

        $products = Product::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'products'));
    }

    /**
     * Update inventory record and adjust product stock accordingly
     */
    public function update(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        // Only allow updating very recent inventory entries
        $cutoffTime = Carbon::now()->subHours(24);
        if ($inventory->created_at->lt($cutoffTime)) {
            return redirect()->route('inventory.show', $inventory)
                ->with('error', 'Cannot edit inventory records older than 24 hours.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|not_in:0',
            'batch_number' => 'nullable|string|max:100',
            'manufactured_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufactured_date',
            'reason' => 'required|string|max:255',
        ]);

        $product = $inventory->product;

        // Calculate the difference in quantity
        $quantityDifference = $validated['quantity'] - $inventory->quantity;
        $newStock = $product->stock + $quantityDifference;

        // Prevent negative stock
        if ($newStock < 0) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'Cannot reduce stock below zero. Current stock: ' . $product->stock]);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update inventory record
            $inventory->update([
                'quantity' => $validated['quantity'],
                'batch_number' => $validated['batch_number'],
                'manufactured_date' => $validated['manufactured_date'],
                'expiry_date' => $validated['expiry_date'],
                'reason' => $validated['reason'],
            ]);

            // Update product stock
            $product->stock = $newStock;
            $product->save();

            DB::commit();

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Inventory record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete inventory record and revert stock changes
     */
    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);

        // Only allow deleting very recent inventory entries
        $cutoffTime = Carbon::now()->subHours(24);
        if ($inventory->created_at->lt($cutoffTime)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete inventory records older than 24 hours.'
                ]);
            }

            return redirect()->route('inventory.index')
                ->with('error', 'Cannot delete inventory records older than 24 hours.');
        }

        $product = $inventory->product;

        // Calculate new stock after reverting this inventory change
        $newStock = $product->stock - $inventory->quantity;

        // Prevent negative stock
        if ($newStock < 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this record as it would result in negative stock.'
                ]);
            }

            return redirect()->route('inventory.index')
                ->with('error', 'Cannot delete this record as it would result in negative stock.');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update product stock
            $product->stock = $newStock;
            $product->save();

            // Delete inventory record
            $inventory->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }

            return redirect()->route('inventory.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Export inventory data
     */
    public function export(Request $request)
    {
        \Illuminate\Support\Facades\Auth::user()->can('viewAny', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        $filters = $request->only(['product', 'type', 'date_from', 'date_to', 'batch']);
        return Excel::download(new InventoryExport($filters), 'inventory-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Show batches expiring soon
     */
    public function expiringSoon()
    {
        \Illuminate\Support\Facades\Auth::user()->can('viewAny', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        $threeMonthsFromNow = Carbon::now()->addMonths(3);

        $expiring = Inventory::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', $threeMonthsFromNow)
            ->where('quantity', '>', 0) // Only include additions, not reductions
            ->with(['product', 'user'])
            ->orderBy('expiry_date')
            ->get()
            ->groupBy('product_id');

        return view('inventory.expiring', compact('expiring'));
    }

    /**
     * Quick stock adjustment for a product
     */
    public function quickAdjust(Request $request)
    {
        \Illuminate\Support\Facades\Auth::user()->can('create', Inventory::class)
            ?: abort(403, 'This action is unauthorized.');

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $newStock = $product->stock + $validated['adjustment'];

        // Prevent negative stock
        if ($newStock < 0) {
            return back()
                ->with('error', 'Cannot adjust stock below zero.');
        }

        // Create inventory record and update product stock
        DB::beginTransaction();

        try {
            // Create inventory record
            Inventory::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['adjustment'],
                'reason' => $validated['reason'],
                'user_id' => auth()->id(),
            ]);

            // Update product stock
            $product->stock = $newStock;
            $product->save();

            DB::commit();

            return back()->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $inventoryCount = Inventory::count();
        $lowStock = Inventory::where('quantity', '<', 10)->get();
        $expiringSoon = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();
        $supplierCount = \App\Models\User::where('role', 'supplier')->count();
        $suppliers = \App\Models\User::where('role', 'supplier')->get();
        $pendingShipments = Shipment::where('status', 'pending')->get();
        $recentActivity = Inventory::orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'time_ago' => $item->updated_at->diffForHumans(),
                    'description' => "Inventory for {$item->product->name} updated. Qty: {$item->quantity}",
                ];
            });
        $view = auth()->user()->role === 'retailer' ? 'dashboard.retailer' : 'dashboard.supplier';
        return view($view, compact(
            'inventoryCount',
            'lowStock',
            'expiringSoon',
            'supplierCount',
            'suppliers',
            'pendingShipments',
            'recentActivity'
        ));
    }

    public function checkStockAlert()
    {
        $lowStock = Inventory::where('quantity', '<', 10)->get();
        $expiringSoon = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();

        if ($lowStock->count() > 0 || $expiringSoon->count() > 0) {
            Notification::route('mail', env('MAIL_USERNAME'))->notify(
                new StockAlertNotification($lowStock, $expiringSoon)
            );
            return back()->with('success', 'Stock alert email sent!');
        }

        return back()->with('info', 'No low stock or expiring items found.');
    }
}
