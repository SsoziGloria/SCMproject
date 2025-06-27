<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load user and supplier relationships for display (both from users table)
        $orders = Order::with([
            'user',
            'supplier' // supplier is a user with role 'supplier'
        ])
            ->orderByDesc('ordered_at')
            ->get();

        return view('order.incoming', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all users with role 'supplier' for selection
        $suppliers = \App\Models\User::where('role', 'supplier')->get();
        $retailers = \App\Models\User::where('role', 'retailer')->get();
        return view('orders.create', compact('suppliers', 'retailers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|unique:orders,order_number',
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'nullable|exists:users,id', // supplier is a user
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'shipping_address' => 'nullable|string',
            'ordered_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        $order = Order::create($request->all());

        return redirect()->route('orders.incoming')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'supplier'])->findOrFail($id);
        return view('order.orderdetails', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $suppliers = \App\Models\User::where('role', 'supplier')->get();
        $retailers = \App\Models\User::where('role', 'retailer')->get();
        return view('orders.edit', compact('order', 'suppliers', 'retailers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'order_number' => 'required|unique:orders,order_number,' . $order->id,
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'nullable|exists:users,id', // supplier is a user
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'shipping_address' => 'nullable|string',
            'ordered_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.incoming')->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.incoming')->with('success', 'Order deleted successfully.');
    }
}
