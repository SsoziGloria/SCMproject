<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    /**
     * Display shipments list
     */
    public function index(Request $request): View
    {
        $query = Shipment::with(['order.user', 'supplier', 'product']);

        // Filter by type
        $type = $request->get('type', 'orders'); // 'orders' or 'suppliers'

        if ($type === 'orders') {
            $query->forOrders();
        } else {
            $query->forSuppliers();
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by supplier (for supplier shipments)
        if ($type === 'suppliers' && $request->filled('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        // Role-based filtering
        if (Auth::check() && Auth::user()->role === 'supplier') {
            $query->where('supplier_id', Auth::user()->id);
        }

        $shipments = $query->latest()->paginate(15);

        // Get counts for badges
        $orderShipmentsCount = Shipment::forOrders()->count();
        $supplierShipmentsCount = Shipment::forSuppliers()->count();

        // Get filter options
        $suppliers = User::where('role', 'supplier')->pluck('name', 'id');
        $orders = Order::with('user:id,name')->select('id', 'order_number', 'user_id')->get();

        return view('shipments.index', compact(
            'shipments',
            'type',
            'orderShipmentsCount',
            'supplierShipmentsCount',
            'suppliers',
            'orders'
        ));
    }

    /**
     * Show the form for creating a new shipment
     */
    public function create(Request $request): View
    {
        $type = $request->get('type', 'orders'); // Default to orders instead of suppliers
        $suppliers = User::where('role', 'supplier')->get();
        $products = Product::all();

        // Only get orders that can be shipped (not already shipped, delivered, or cancelled)
        $orders = Order::whereNotIn('status', ['shipped', 'delivered', 'cancelled'])->get();

        return view('shipments.create', compact('type', 'suppliers', 'products', 'orders'));
    }

    /**
     * Store a newly created shipment
     */
    public function store(Request $request): RedirectResponse
    {
        // Debug: Log the incoming request data
        \Log::info('Shipment creation request:', $request->all());

        $rules = [
            'type' => 'required|in:orders,suppliers',
            'expected_delivery' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ];

        // Add conditional validation based on type
        if ($request->type === 'orders') {
            $rules['order_id'] = 'required|exists:orders,id';
        } elseif ($request->type === 'suppliers') {
            $rules['supplier_id'] = 'required|exists:users,id';
            $rules['product_id'] = 'required|exists:products,id';
            $rules['quantity'] = 'required|integer|min:1';
        }

        $request->validate($rules, [
            'order_id.required' => 'Please select an order for the shipment.',
            'supplier_id.required' => 'Please select a supplier for the shipment.',
            'product_id.required' => 'Please select a product for the shipment.',
            'quantity.required' => 'Please enter a quantity for the shipment.',
            'quantity.min' => 'Quantity must be at least 1.',
        ]);

        $shipmentData = [
            'status' => 'processing',
            'expected_delivery' => $request->expected_delivery ?: now()->addDays(3),
            'notes' => $request->notes,
        ];

        if ($request->type === 'orders') {
            // Order shipment
            $shipmentData['order_id'] = $request->order_id;

            // Mark order as shipped using the Order model method
            $order = Order::findOrFail($request->order_id);
            $order->markAsShipped($request->expected_delivery);

            return redirect()->route('shipments.index', ['type' => 'orders'])
                ->with('success', 'Order shipment created successfully!');
        } else {
            // Supplier shipment
            $shipmentData = array_merge($shipmentData, [
                'supplier_id' => $request->supplier_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);

            Shipment::create($shipmentData);

            return redirect()->route('shipments.index', ['type' => 'suppliers'])
                ->with('success', 'Supplier shipment created successfully!');
        }
    }

    /**
     * Display the specified shipment
     */
    public function show(Shipment $shipment): View
    {
        $shipment->load(['order.user', 'supplier', 'product']);
        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified shipment
     */
    public function edit(Shipment $shipment): View
    {
        $suppliers = User::where('role', 'supplier')->get();
        $products = Product::all();
        $orders = Order::all();

        return view('shipments.edit', compact('shipment', 'suppliers', 'products', 'orders'));
    }

    /**
     * Update the specified shipment
     */
    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,in_transit,delivered',
            'expected_delivery' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $shipment->updateStatus($request->status);

        $shipment->update([
            'expected_delivery' => $request->expected_delivery,
            'notes' => $request->notes,
        ]);

        $type = $shipment->order_id ? 'orders' : 'suppliers';

        return redirect()->route('shipments.index', ['type' => $type])
            ->with('success', 'Shipment updated successfully!');
    }

    /**
     * Update shipment status
     */
    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,in_transit,delivered',
        ]);

        $shipment->updateStatus($request->status);

        return back()->with('success', 'Shipment status updated successfully!');
    }

    /**
     * Remove the specified shipment
     */
    public function destroy(Shipment $shipment): RedirectResponse
    {
        $type = $shipment->order_id ? 'orders' : 'suppliers';
        $shipment->delete();

        return redirect()->route('shipments.index', ['type' => $type])
            ->with('success', 'Shipment deleted successfully!');
    }
}
