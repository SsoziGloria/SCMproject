<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;

class InventoryController extends Controller
{
    /**
     * Display a paginated listing of the inventory.
     */
    public function index()
    {
        $inventories = Inventory::paginate(25); 
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
}