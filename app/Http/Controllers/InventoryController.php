<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;
use App\Models\Adjustment;
use App\Models\InventoryAdjustment;
use App\Models\Order;

class InventoryController extends Controller
{
    /**
     * Display a paginated listing of the inventory.
     */
    public function index(Request $request)
    {
        // Base query
        $query = Inventory::with(['product', 'supplier']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('batch_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('expiration')) {
            if ($request->expiration === 'soon') {
                $query->expiringSoon();
            } elseif ($request->expiration === 'expired') {
                $query->expired();
            }
        }

        // Sort options
        $sort = $request->get('sort', 'newest');
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'quantity_asc') {
            $query->orderBy('quantity', 'asc');
        } elseif ($sort === 'quantity_desc') {
            $query->orderBy('quantity', 'desc');
        } elseif ($sort === 'expiry') {
            $query->whereNotNull('expiration_date')
                ->orderBy('expiration_date', 'asc');
        }

        $inventory = $query->paginate(15);

        $suppliers = Supplier::where('status', 'active')->get();

        // Stats for dashboard cards
        $stats = [
            'total_items' => Inventory::sum('quantity'),
            'product_count' => Inventory::distinct('product_id')->count(),
            'low_stock_count' => Inventory::lowStock()->count(),
            'expiring_soon_count' => Inventory::expiringSoon()->count(),
        ];

        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])
            ->with('items')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                $order->status_color = match ($order->status) {
                    'pending' => 'warning',
                    'processing' => 'info',
                    default => 'secondary'
                };

                $tooltipContent = '';
                foreach ($order->items as $item) {
                    $available = Inventory::where('product_id', $item->product_id)
                        ->where('status', 'available')
                        ->sum('quantity');

                    $tooltipContent .= "<div class='mb-2'><strong>{$item->product_name}</strong><br>";
                    $tooltipContent .= "Ordered: {$item->quantity}<br>";
                    $tooltipContent .= "Available: {$available}</div>";
                }

                $order->products_tooltip = $tooltipContent;

                return $order;
            });

        $recentAdjustments = InventoryAdjustment::with('inventory')
            ->latest()
            ->take(10)
            ->get();

        return view('inventory.index', compact('inventory', 'suppliers', 'stats', 'pendingOrders', 'recentAdjustments'));
    }

    /**
     * Show the form for creating a new inventory record.
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('inventories.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created inventory record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'location' => 'required|string',
            'expiration_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        try {
            $data = $request->only(['product_id', 'product_name', 'quantity', 'location', 'expiration_date', 'supplier_id']);
            Inventory::create($data);
            return redirect()->route('inventories.create')->with('success', 'Inventory submitted.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to submit inventory: ' . $e->getMessage()]);
        }
    }
    //for displaying all inventories
    public function retailerIndex()
    {
        $inventories = Inventory::all();
        return view('inventories.index', compact('inventories'));
    }

    public function retailerCreate()
    {
        $products = Product::all();
        return view('inventories.create', compact('products'));

    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('inventories.edit', compact('inventory', 'products', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'location' => 'required|string',
            'expiration_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $data = $request->only(['product_id', 'product_name', 'quantity', 'location', 'expiration_date', 'supplier_id']);
        $inventory->update($data);

        $lowStock = Inventory::where('quantity', '<', 10)->get();
        $expiringSoon = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();

        //send notification if there's low stock or expiring soon items
        if ($lowStock->count() > 0 || $expiringSoon->count() > 0) {
            Notification::route('mail', env('MAIL_USERNAME'))->notify(new StockAlertNotification($lowStock));
        }
        return redirect()->route('dashboard')->with('success', 'Inventory updated.');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);

        // Create a record of this deletion in inventory adjustments
        InventoryAdjustment::create([
            'inventory_id' => $inventory->id,
            'adjustment_type' => 'removal',
            'quantity_change' => $inventory->quantity,
            'reason' => 'Inventory record deleted',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
        ]);

        $inventory->delete();

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory record deleted successfully.');
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
        $inventoryCount = Inventory::count();
        $lowStock = Inventory::where('quantity', '<', 10)->get();
        $expiringSoon = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();
        $supplierCount = \App\Models\User::where('role', 'supplier')->count();

        if ($lowStock->count() > 0 || $expiringSoon->count() > 0) {
            Notification::route('mail', 'irenemargi256@gmail.com')->notify(new StockAlertNotification($lowStock));
        }

        return view('dashboard.supplier', compact('inventoryCount', 'lowStock', 'expiringSoon', 'supplierCount'));
    }
    public function stockLevels()
    {
        $lowStock = Inventory::where('quantity', '<=', 10)->count();
        $criticalStock = Inventory::where('quantity', '<=', 5)->count();
        $outOfStock = Inventory::where('quantity', '=', 0)->count();

        $inventorySummary = Inventory::selectRaw('
            COUNT(*) as total_records,
            SUM(CASE WHEN quantity <= 5 THEN 1 ELSE 0 END) as critical,
            SUM(CASE WHEN quantity > 5 AND quantity <= 10 THEN 1 ELSE 0 END) as low,
            SUM(CASE WHEN quantity > 10 THEN 1 ELSE 0 END) as adequate,
            SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock
        ')
            ->first();

        return view('stockLevels.index', compact('lowStock', 'criticalStock', 'outOfStock', 'inventorySummary'));
    }

    public function reorders()
    {
        $reorders = Inventory::with('supplier')
            ->where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->get();

        return view('inventories.reorders', compact('reorders'));
    }


    public function adjustments()
    {
        $adjustments = Adjustment::with('inventory')->get();
        return view('inventories.adjustments', compact('adjustments'));
    }
    public function createAdjustment()
    {
        $inventories = Inventory::orderBy('product_name')
            ->get();

        return view('inventories.adjustments_create', compact('inventories'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment_type' => 'required|in:increase,decrease,correction,damage,expiry',
            'quantity_change' => 'required|integer|min:1',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::findOrFail($validated['inventory_id']);

        // Calculate new quantity based on adjustment type
        $oldQuantity = $inventory->quantity;
        $newQuantity = $oldQuantity;

        switch ($validated['adjustment_type']) {
            case 'increase':
                $newQuantity = $oldQuantity + $validated['quantity_change'];
                break;
            case 'decrease':
            case 'damage':
            case 'expiry':
                $newQuantity = $oldQuantity - $validated['quantity_change'];
                if ($newQuantity < 0) {
                    return back()->withErrors(['quantity_change' => 'Cannot decrease more than the current quantity.'])->withInput();
                }
                break;
            case 'correction':
                $newQuantity = $validated['quantity_change'];
                break;
        }

        // Create adjustment record
        $adjustment = InventoryAdjustment::create([
            'inventory_id' => $validated['inventory_id'],
            'adjustment_type' => $validated['adjustment_type'],
            'quantity_change' => $validated['adjustment_type'] === 'correction'
                ? abs($newQuantity - $oldQuantity)
                : $validated['quantity_change'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
        ]);

        // Update inventory quantity
        $inventory->quantity = $newQuantity;

        // Update status based on adjustment type if needed
        if ($validated['adjustment_type'] === 'damage') {
            $inventory->status = 'damaged';
        } elseif ($validated['adjustment_type'] === 'expiry') {
            $inventory->status = 'expired';
        }

        $inventory->save();

        return redirect()->route('inventories.adjustments')
            ->with('success', 'Inventory adjustment recorded successfully.');
    }
}