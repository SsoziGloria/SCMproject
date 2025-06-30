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
        // Fetch suppliers with status 'pending' 
        $suppliers = Supplier::where('status', 'pending')->get();
        return view('supplier.requests', compact('suppliers'));
    }

    public function orders()
    {
        return view('supplier.orders');
    }

    public function messages()
    {
        return view('supplier.messages');
    }

    public function showRegisterForm()
    {
        return view('suppliers.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'company' => 'nullable|string|max:255',
        ]);

        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'status' => 'pending', // default status
        ]);

        return redirect()->route('suppliers.register.form')->with('success', 'Your request has been submitted and is pending approval.');
    }
}
