<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Shipment;
use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;
use App\Models\Adjustment;

class InventoryController extends Controller
{
    /**
     * Display a paginated listing of the inventory.
     */
    public function index()
    {
        $inventories = Inventory::with(['product', 'supplier'])->paginate(25); // Eager load relations
        return view('inventories.index', compact('inventories'));


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
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Inventory deleted.');
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

        if ($lowStock->count() > 0 || $expiringSoon->count() > 0) {
            Notification::route('mail', 'irenemargi256@gmail.com')->notify(new StockAlertNotification($lowStock));
        }

        return view('dashboard.supplier', compact('inventoryCount', 'lowStock', 'expiringSoon'));
    }
    public function stockLevels()
    {
        $products = Product::with('category')->get();
        return view('stockLevels.index', compact('products'));
    }

    public function reorders()
    {
        // Show inventories that are low in stock
        $reorders = Inventory::where('quantity', '<', 10)->get();
        return view('inventories.reorders', compact('reorders'));
    }


    public function adjustments()
    {
        $adjustments = Adjustment::with('inventory')->get();
        return view('inventories.adjustments', compact('adjustments'));
    }
    public function createAdjustment()
    {
        $inventories = Inventory::all();
        return view('inventories.adjustments_create', compact('inventories'));
    }
}