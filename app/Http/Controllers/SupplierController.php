<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Inventory;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }
    public function dashboard()
{
    $inventoryCount = Inventory::count();


        return view('dashboard', compact('inventoryCount'));

}

public function approved()
{
    $suppliers = Supplier::all();
    return view('supplier.approved', compact('suppliers'));
}

public function requests()
{
    return view('supplier.requests');
}

public function orders()
{
    return view('supplier.orders');
}

public function messages()
{
    return view('supplier.messages');
}
}
