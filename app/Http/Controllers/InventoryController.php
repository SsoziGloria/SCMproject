<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Carbon\Carbon;

class InventoryController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
        'id' => 'required|numeric',
        'product_name' => 'required|string',
        'quantity' => 'required|integer',
        'location' => 'required|string',
        'expiration_date' => 'required|date',
        ]);

        Inventory::create($request->all());

    return redirect()->route('inventory.index')->with('success', 'Inventory added.');
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

    return redirect()->route('inventories.index')->with('success', 'Inventory updated.');
}

public function destroy($id)
{
    $inventory = Inventory::findOrFail($id);
    $inventory->delete();

    return redirect()->route('inventories.index')->with('success', 'Inventory deleted.');
}

public function dashboard()
{
    $lowStock = Inventory::where('quantity', '<', 10)->get();
    $nearExpiry = Inventory::where('expiration_date', '<=', Carbon::now()->addDays(25))->get();

    return view('dashboard', compact('lowStock', 'nearExpiry'));
}

    }

    /**
     * Display the specified resource.
     */

