<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;

class InventoryController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
        'product_id' => 'required|numeric',
        'product_name' => 'required|string',
        'quantity' => 'required|string',
        'location' => 'required|string',
        'expiration_date' => 'required|date',
        ]);

        Inventory::create($request->all());

    return redirect()->route('dashboard')->with('success', 'Inventory added.');
}

public function index()
{
    $inventories = Inventory::all();
    return view('inventories.index', compact('inventories'));
}

public function create(){
    return view('inventories.create');
}

public function edit($id)
{
    $inventory = Inventory::findOrFail($id);
    return view('inventories.edit', compact('inventory'));
}

public function update(Request $request, $id)
{
    $inventory = Inventory::findOrFail($id);

    $request->validate([
        'id' => 'required|numeric',
        'product_name' => 'required|string',
        'quantity' => 'required|integer',
        'location' => 'required|string',
        'expiration_date' => 'required|date',
        ]);

    $inventory->update($request->all());

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
    $inventoryCount = \App\Models\Inventory::count(); 
    $lowStock = \App\Models\Inventory::where('quantity', '<', 10)->get();
    $nearExpiry = \App\Models\Inventory::where('expiration_date', '<=', Carbon::now()->addDays(30))->get();

if ($lowStock->count() > 0 || $nearExpiry->count() > 0) {
    Notification::route('mail', 'admin@emma.com')->notify(new StockAlertNotification());
}
return view('dashboard', compact('inventoryCount', 'lowStock', 'nearExpiry'));


}



    }

    
