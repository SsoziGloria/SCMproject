<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\SalesChannel;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
<<<<<<< HEAD
    {
        $this->middleware('auth');
    }

    // List all orders for the authorized user
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['items.product', 'user']);

        // Role-based filtering
        if ($user->role === 'supplier') {
            $query->whereHas('items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            });
        } elseif ($user->role === 'retailer' || $user->role === 'admin') {
            // Show all orders
            if ($user->role === 'retailer' && $user->retailer) {
                $query->where('retailer_id', $user->retailer->id);
            }
        } else {
            // Customer: only their own orders
            $query->where('user_id', $user->id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sales channel filter
        if ($request->filled('sales_channel')) {
            $query->where('sales_channel_id', $request->sales_channel);
        }

        // Search filter (order number, customer name, or product name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('items.product', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'total_high':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'total_low':
                $query->orderBy('total_amount', 'asc');
                break;
            default:
                $query->latest();
        }

        // Pagination
        $orders = $query->paginate(15)->withQueryString();

        // Stats (for dashboard cards)
        $statsQuery = Order::query();

        if ($user->role === 'retailer' && $user->retailer) {
            $statsQuery->where('retailer_id', $user->retailer->id);
        }

        $stats = [
            'total_orders' => $statsQuery->count(),
            'total_revenue' => $statsQuery->sum('total_amount'),
            'pending_orders' => (clone $statsQuery)->where('status', 'pending')->count(),
            'shipped_orders' => (clone $statsQuery)->where('status', 'shipped')->count(),
        ];

        // Get sales channels for filter dropdown
        $salesChannels = SalesChannel::where('is_active', true)->get();

        return view('orders.index', compact('orders', 'stats', 'salesChannels'));
    }

<<<<<<< Updated upstream
    // Show the form for creating a new order
=======
=======
    {
        $this->middleware('auth');
    }

    //list all orders for the authorized retailer
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('product')
            ->orderBy('order_date', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
    //show the form for creating a new order
>>>>>>> Stashed changes
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $salesChannels = SalesChannel::where('is_active', true)->get();
        return view('orders.create', compact('products', 'salesChannels'));
    }

    // Store a new order (POS/Admin created orders)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_region' => 'nullable|string|max:100',
            'sales_channel_id' => 'required|exists:sales_channels,id',
            'payment' => 'required|string',
            'payment_status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Generate order number
        $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // Calculate totals
        $subtotal = 0;
        $items = [];

        // Begin transaction
        DB::beginTransaction();

        try {
            // Verify stock and calculate subtotal
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return back()->withErrors(['items' => "Not enough stock for {$product->name}. Only {$product->stock} available."]);
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'unit_cost' => $product->cost,
                    'product_name' => $product->name,
                    'product_category' => $product->category,
                    'subtotal' => $itemTotal
                ];
            }

            // Apply discounts (if any)
            $discountAmount = $request->input('discount_amount', 0);

            // Calculate shipping fee
            $shippingFee = $request->input('shipping_fee', 0);

            // Calculate total
            $totalAmount = $subtotal - $discountAmount + $shippingFee;

            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $request->input('user_id') ?? Auth::id(),
                'phone' => $validated['phone'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_region' => $validated['shipping_region'],
                'sales_channel_id' => $validated['sales_channel_id'],
                'sales_channel' => SalesChannel::find($validated['sales_channel_id'])->name,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment' => $validated['payment'],
                'payment_status' => $validated['payment_status'],
                'notes' => $validated['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create order items and update inventory
            foreach ($items as $item) {
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_category' => $item['product_category'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    // Show a single order
    public function show(Order $order)
    {
        // Authorize view permission
        $this->authorizeView($order);

        // Calculate profit for admin/retailer users
        $profit = 0;
        if (in_array(Auth::user()->role, ['admin', 'retailer'])) {
            foreach ($order->items as $item) {
                $profit += ($item->price - ($item->unit_cost ?? 0)) * $item->quantity;
            }
        }

        $username = $order->user ? $order->user->name : 'Guest';
        $userEmail = $order->user ? $order->user->email : null;

        return view('orders.show', compact('order', 'profit', 'username', 'userEmail'));
    }

<<<<<<< Updated upstream
    // Show the form for editing the specified order
=======

<<<<<<< HEAD
=======

>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
    // Show the form for editing the specified resource.

>>>>>>> Stashed changes
    public function edit(Order $order)
    {
        // Authorize edit permission
        $this->authorizeEdit($order);

        $salesChannels = SalesChannel::where('is_active', true)->get();
        $products = Product::where('stock', '>', 0)->get();

        return view('orders.edit', compact('order', 'products', 'salesChannels'));
    }

    // Update the specified order
    public function update(Request $request, Order $order)
    {
        // Authorize edit permission
        $this->authorizeEdit($order);

        // Validate based on what's being updated
        if ($request->has('status') && !$request->has('items')) {
            // Just updating status
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
                'payment_status' => 'sometimes|in:pending,paid,failed',
                'notes' => 'nullable|string',
            ]);

            $order->update($validated);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order status updated successfully!');
        }

        // Full order update
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_region' => 'nullable|string|max:100',
            'sales_channel_id' => 'required|exists:sales_channels,id',
            'payment' => 'required|string',
            'payment_status' => 'required|in:pending,paid,failed',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update sales channel name
            $validated['sales_channel'] = SalesChannel::find($validated['sales_channel_id'])->name;

            // Update order details
            $order->update($validated);

            // If updating items too
            if ($request->has('items') && is_array($request->items)) {
                // First restore stock for all current items
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }

                // Delete current items
                $order->items()->delete();

                // Add new items
                $subtotal = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Not enough stock for {$product->name}. Only {$product->stock} available.");
                    }

                    $itemTotal = $product->price * $item['quantity'];
                    $subtotal += $itemTotal;

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_category' => $product->category,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'unit_cost' => $product->cost,
                        'subtotal' => $itemTotal
                    ]);

                    // Update product stock
                    $product->stock -= $item['quantity'];
                    $product->save();
                }

                // Update order subtotal and total
                $order->subtotal = $subtotal;
                $order->total_amount = $subtotal - $order->discount_amount + $order->shipping_fee;
                $order->save();
            }

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    // Delete an order
    public function destroy(Order $order)
    {
        // Authorize delete permission
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
<<<<<<< Updated upstream

        // Begin transaction
        DB::beginTransaction();

        try {
            // Restore stock for all items
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Delete order items and then order
            $order->items()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }
=======
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted!');
<<<<<<< HEAD
=======
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
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
>>>>>>> Stashed changes
    }

    // Export orders to Excel
    public function export()
    {
        return Excel::download(new OrderExport, 'orders-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    // Filter orders by status
    public function filterByStatus($status)
    {
        $user = Auth::user();
        $query = Order::with(['items.product', 'user'])->where('status', $status);

        // Role-based filtering
        if ($user->role === 'supplier') {
            $query->whereHas('items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            });
        } elseif ($user->role === 'retailer' && $user->retailer) {
            $query->where('retailer_id', $user->retailer->id);
        } elseif ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $orders = $query->paginate(15);
        $statusText = ucfirst($status);

        return view('orders.filtered', compact('orders', 'status', 'statusText'));
    }

    // Helper method to authorize view permission
    private function authorizeView(Order $order)
    {
        $user = Auth::user();

        // Admin can view all orders
        if ($user->role === 'admin') {
            return true;
        }

        // Retailer can view their store's orders
        if ($user->role === 'retailer' && $user->retailer) {
            if ($order->retailer_id === $user->retailer->id) {
                return true;
            }
        }

        // Supplier can view orders with their products
        if ($user->role === 'supplier') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->supplier_id === $user->id) {
                    return true;
                }
            }
        }

        // Customer can view their own orders
        if ($order->user_id === $user->id) {
            return true;
        }

        abort(403, 'Unauthorized to view this order.');
    }

    // Helper method to authorize edit permission
    private function authorizeEdit(Order $order)
    {
        $user = Auth::user();

        // Admin can edit all orders
        if ($user->role === 'admin') {
            return true;
        }

        // Retailer can edit their store's orders if not delivered/cancelled
        if ($user->role === 'retailer' && $user->retailer) {
            if (
                $order->retailer_id === $user->retailer->id &&
                !in_array($order->status, ['delivered', 'cancelled'])
            ) {
                return true;
            }
        }

        abort(403, 'Unauthorized to edit this order.');
    }

    // Update order status only
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}