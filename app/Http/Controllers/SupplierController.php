<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Supplier::where('supplier_id', auth()->id())->first();

        $query = Product::where('supplier_id', $supplier->supplier_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $products = $query->with(['category'])->paginate(10);

        $suppliers = Supplier::all();

        return view('supplier.products.index', compact('products', 'suppliers'));
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
