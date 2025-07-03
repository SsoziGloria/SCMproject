<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //list all orders for the authorized retailer
    public function index(Request $request)
    {
        if (Auth::user()->role == 'user') {
            $orders = Order::where('user_id', Auth::id())->latest()->paginate(15);
        } else {
            $orders = Order::paginate(15);
        }

        $query = Order::query();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Optionally calculate total_orders (could be a custom query or just $orders->total())
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
        ];

        return view('orders.index', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    //show the form for creating a new order
    public function create()
    {
        // Fetch all users with role 'supplier' for selection
        //$suppliers = \App\Models\User::where('role', 'supplier')->get();
        //$retailers = \App\Models\User::where('role', 'retailer')->get();
        $products = \App\Models\Product::all();
        return view('orders.create', compact('products'));
    }

    //store a new order
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Checks inventory and prioritizes by the earliest expiration
        $inventory = Inventory::where('product_id', $validated['product_id'])
            ->where('quantity', '>=', $validated['quantity'])
            ->orderBy('expiration_date', 'asc')
            ->first();

        if (!$inventory) {
            return back()->withErrors(['quantity' => 'Not enough inventory or product not available.']);
        }

        $orderNumber = 'ORD-' . strtoupper(uniqid());

        // Create order directly
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'status' => 'pending',
            'order_date' => now(),
        ]);

        return redirect()->route('orders.index')->with('success', 'Order submitted and pending approval.');
    }

    //shows a  single order
    public function show(Order $order)
    {
        //$product = Inventory::all();
        //return view('orders.create', compact('order'));
        return view('orders.show', compact('order'));

    }



    // Show the form for editing the specified resource.

    public function edit(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $products = Inventory::all();
        return view('orders.edit', compact('order', 'products'));
    }


    // Update order
    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);
        return redirect()->route('orders.index')->with('success', 'Order updated!');
    }

    // Delete order
    public function destroy(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted!');
    }

    //FOR THE SIDE BAR

    //pending orders
    public function pending()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'pending')->with('product')->get();
        return view('orders.pending', compact('orders'));
    }

    //orders in progress
    public function inProgress()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'in_progress')->with('product')->get();
        return view('orders.in_progress', compact('orders'));
    }

    //completed orders
    public function completed()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')->with('product')->get();
        return view('orders.completed', compact('orders'));
    }

    //cancelled orders
    public function cancelled()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'cancelled')->with('product')->get();
        return view('orders.cancelled', compact('orders'));
    }

    public function dashboard()
    {
        $deliveredOrders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();



        return view('dashboard.retailer', compact('deliveredOrders'));
    }

    public function export()
    {
        return Excel::download(new OrderExport, 'orders.xlsx');
    }
}
