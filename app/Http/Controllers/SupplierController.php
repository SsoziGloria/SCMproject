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
}
