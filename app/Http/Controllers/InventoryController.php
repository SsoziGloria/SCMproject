<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
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
        $inventories = Inventory::all(); 
        return view('inventories.index', compact('inventories'));

        
    }

    /**
     * Show the form for creating a new inventory record.
     */
    public function create()
    {
        return view('inventories.create');
    }

    /**
     * Store a newly created inventory record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'location' => 'required|string',
            'expiration_date' => 'required|date',
        ]);

        Inventory::create($request->all());

        return redirect()->route('dashboard.supplier')->with('success', 'Inventory added.');
    }

    /**
     * Show the form for editing the specified inventory record.
     */
    public function edit(Inventory $inventory)
    {
        return view('inventories.edit', compact('inventory'));
    }

    /**
     * Update the specified inventory record in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'location' => 'required|string',
            'expiration_date' => 'required|date',
        ]);

        $inventory->update($request->all());

        return redirect()->route('dashboard.supplier')->with('success', 'Inventory updated.');
    }

    /**
     * Remove the specified inventory record from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventories.index')->with('success', 'Inventory deleted.');
    }

    /**
     * Show the supplier dashboard with inventory stats.
     */
    public function dashboard()
    {
        $inventoryCount = Inventory::count();
        $lowStock = Inventory::where('quantity', '<', 10)->get();
        $nearExpiry = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();

        if ($lowStock->count() > 0 || $nearExpiry->count() > 0) {
            Notification::route('mail', 'admin@emma.com')->notify(new StockAlertNotification());
        }

        return view('dashboard.supplier', compact('inventoryCount', 'lowStock', 'nearExpiry'));
    }

    
public function stockLevels()
{
    $products = Product::paginate(25);
    return view('stockLevels.index', compact('products'));
}

public function show(Inventory $inventory)
{
    
    return view('inventories.show', compact('inventory'));
}
//products where reorders are below...
public function reorders()
{
    
    $reorders = Inventory::with('product')
        ->whereColumn('quantity', '<', 'reorder_level')
        ->get();

    return view('inventories.reorders', compact('reorders'));
}

// List all adjustments
public function adjustments()
{
    $adjustments = Adjustment::with('inventory')->latest()->get();
    return view('inventories.adjustments', compact('adjustments'));
}

// Show form to create a new adjustment
public function createAdjustment()
{
    $inventories = Inventory::all();
    return view('inventories.adjustments_create', compact('inventories'));
}

// Store a new adjustment
public function storeAdjustment(Request $request)
{
    $request->validate([
        'inventory_id' => 'required|exists:inventories,id',
        'type' => 'required|in:add,remove',
        'amount' => 'required|integer|min:1',
        'reason' => 'nullable|string|max:255',
    ]);

    $adjustment = Adjustment::create($request->all());

    // Update inventory quantity
    $inventory = $adjustment->inventory;
    if ($request->type === 'add') {
        $inventory->quantity += $request->amount;
    } else {
        $inventory->quantity -= $request->amount;
    }
    $inventory->save();

    return redirect()->route('inventories.adjustments')->with('success', 'Adjustment recorded.');
}
}