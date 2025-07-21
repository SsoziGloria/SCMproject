<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierDashboardController extends Controller
{
    private $supplier;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $supplier = Supplier::where('supplier_id', Auth::id())->first();

        if (!$supplier) {
            return redirect()->route('vendor.verification.form')
                ->with('error', 'You must complete your supplier profile before accessing the dashboard.');
        }

        $supplierId = $supplier->supplier_id;

        $productsQuery = Product::where('supplier_id', $supplierId);

        $stats = [
            'totalProducts' => (clone $productsQuery)->count(),
            'lowStockCount' => (clone $productsQuery)->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'totalInventoryValue' => (clone $productsQuery)->sum(DB::raw('price * stock')),
            'pendingOrders' => Order::whereHas('products', fn($q) => $q->where('supplier_id', $supplierId))
                ->where('status', 'pending')->count(),
        ];

        $lowStockProducts = (clone $productsQuery)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $recentOrders = Order::whereHas('products', fn($q) => $q->where('supplier_id', $supplierId))
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.supplier', compact('stats', 'lowStockProducts', 'recentOrders'));
    }
}
