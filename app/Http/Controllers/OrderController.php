<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Shipment;
use App\Models\SalesChannel;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryAdjustment;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List all orders for the authorized user
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['items.product', 'user']);

        if ($user->role === 'supplier') {
            $query->whereHas('items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            });
        } elseif ($user->role === 'retailer' || $user->role === 'admin') {
            if ($user->role === 'retailer' && $user->retailer) {
                $query->where('retailer_id', $user->retailer->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('sales_channel')) {
            $query->where('sales_channel_id', $request->sales_channel);
        }

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

        $orders = $query->paginate(15)->withQueryString();

        $statsQuery = Order::query();

        if ($user->role === 'retailer' && $user->retailer) {
            $statsQuery->where('retailer_id', $user->retailer->id);
        }

        $stats = [
            'total_orders' => $statsQuery->count(),
            'total_revenue' => (clone $statsQuery)->where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => (clone $statsQuery)->where('status', 'pending')->count(),
            'shipped_orders' => (clone $statsQuery)->where('status', 'shipped')->count(),
        ];

        $salesChannels = SalesChannel::where('is_active', true)->get();

        return view('orders.index', compact('orders', 'stats', 'salesChannels'));
    }


    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $salesChannels = SalesChannel::where('is_active', true)->get();
        return view('orders.create', compact('products', 'salesChannels'));
    }

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

        $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $subtotal = 0;
        $items = [];

        DB::beginTransaction();

        try {
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->available_stock < $item['quantity']) {
                    return back()->withErrors(['items' => "Not enough stock for {$product->name}. Only {$product->available_stock} available."]);
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

                $product = Product::find($item['product_id']);
                $product->allocated_stock += $item['quantity'];
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

    public function show(Order $order)
    {
        $this->authorizeView($order);

        $profit = 0;
        if (in_array(Auth::user()->role, ['admin', 'retailer'])) {
            foreach ($order->items as $item) {
                $profit += ($item->price - ($item->unit_cost ?? 0)) * $item->quantity;
            }
        }

        $username = $order->user ? $order->user->name : 'Guest';
        $userEmail = $order->user ? $order->user->email : null;

        $allItemsInStock = true;

        foreach ($order->items as $item) {
            $availableQuantity = Inventory::where('product_id', $item->product_id)
                ->where('status', 'available')
                ->where('quantity', '>', 0)
                ->sum('quantity');

            if ($availableQuantity < $item->quantity - ($item->quantity_shipped ?? 0)) {
                $allItemsInStock = false;
                break;
            }
        }

        // Get inventory status for each item for display
        $itemInventoryStatus = [];
        foreach ($order->items as $item) {
            $availableQuantity = Inventory::where('product_id', $item->product_id)
                ->where('status', 'available')
                ->where('quantity', '>', 0)
                ->sum('quantity');

            $neededQuantity = $item->quantity - ($item->quantity_shipped ?? 0);

            $itemInventoryStatus[$item->id] = [
                'available' => $availableQuantity,
                'needed' => $neededQuantity,
                'sufficient' => $availableQuantity >= $neededQuantity,
                'status_class' => $availableQuantity >= $neededQuantity ? 'success' : ($availableQuantity > 0 ? 'warning' : 'danger')
            ];
        }

        return view('orders.show', compact('order', 'profit', 'username', 'userEmail', 'allItemsInStock'));
    }



    // Show the form for editing the specified resource.

    public function edit(Order $order)
    {
        // Authorize edit permission
        $this->authorizeEdit($order);

        $salesChannels = SalesChannel::where('is_active', true)->get();
        $products = Product::where('stock', '>', 0)->get();

        return view('orders.edit', compact('order', 'products', 'salesChannels'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeEdit($order);

        if ($request->has('status') && !$request->has('items')) {
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
                'payment_status' => 'sometimes|in:pending,paid,failed',
                'notes' => 'nullable|string',
            ]);

            $order->update($validated);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order status updated successfully!');
        }

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

        DB::beginTransaction();

        try {
            $validated['sales_channel'] = SalesChannel::find($validated['sales_channel_id'])->name;

            $order->update($validated);

            if ($request->has('items') && is_array($request->items)) {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        // Prevent allocated_stock from going negative
                        $product->allocated_stock = max(0, $product->allocated_stock - $item->quantity);
                        $product->save();
                    }
                }

                $order->items()->delete();

                $subtotal = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Not enough stock for {$product->name}. Only {$product->stock} available.");
                    }

                    $itemTotal = $product->price * $item['quantity'];
                    $subtotal += $itemTotal;

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

                    $product->stock -= $item['quantity'];
                    $product->save();
                }

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

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Prevent allocated_stock from going negative
                    $product->allocated_stock = max(0, $product->allocated_stock - $item->quantity);
                    $product->save();
                }
            }

            $order->items()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted!');
    }

    public function dashboard()
    {
        $deliveredOrders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();



        return view('dashboard.retailer', compact('deliveredOrders'));
    }

    // Export orders to Excel
    public function export()
    {
        return Excel::download(new OrderExport, 'orders-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function filterByStatus($status)
    {
        $user = Auth::user();
        $query = Order::with(['items.product', 'user'])->where('status', $status);

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

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'retailer') {
            if ($order->sales_channel === 'Online Store') {
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

    private function authorizeEdit(Order $order)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'retailer') {
            if (
                $order->sales_channel === 'online' &&
                !in_array($order->status, ['delivered', 'cancelled'])
            ) {
                return true;
            }
        }

        abort(403, 'Unauthorized to edit this order.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'restore_inventory' => 'nullable|boolean', // For the checkbox
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'Order status is already ' . ucfirst($newStatus));
        }

        DB::beginTransaction();
        try {
            $statusHistory = OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'user_id' => Auth::id(),
                'user_name' => Auth::user() ? Auth::user()->name : 'System',
                'notes' => $request->status_notes ?? "Status changed from {$oldStatus} to {$newStatus}"
            ]);

            if (in_array($newStatus, ['shipped', 'delivered']) && !in_array($oldStatus, ['shipped', 'delivered'])) {
                $this->processShipment($order, $statusHistory);
            }

            if ($newStatus === 'cancelled') {
                $this->processCancellation($order, $oldStatus, $request, $statusHistory);
            }

            $order->status = $newStatus;
            $order->save();

            DB::commit();
            return redirect()->back()->with('success', "Order status updated to " . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status for order #' . $order->order_number . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    public function markAsShipped(Order $order)
    {
        $oldStatus = $order->status;

        $insufficientItems = collect();

        foreach ($order->items as $item) {
            $available = Inventory::where('product_id', $item->product_id)
                ->where('status', 'available')
                ->sum('quantity');

            if ($available < $item->quantity - ($item->quantity_shipped ?? 0)) {
                $insufficientItems->push([
                    'product_name' => $item->product_name,
                    'needed' => $item->quantity - ($item->quantity_shipped ?? 0),
                    'available' => $available
                ]);
            }
        }

        if ($insufficientItems->count() > 0) {
            return redirect()->back()->with(
                'error',
                'Cannot ship order due to insufficient inventory for ' .
                    $insufficientItems->pluck('product_name')->join(', ')
            );
        }

        DB::beginTransaction();

        try {
            // Use the Order model's markAsShipped method which creates shipment records
            $order->markAsShipped();

            $statusHistory = OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'shipped',
                'user_id' => Auth::id(),
                'notes' => 'Order marked as shipped with automatic shipment creation'
            ]);

            foreach ($order->items as $item) {
                if ($item->quantity_shipped < $item->quantity) {
                    $neededQuantity = $item->quantity - $item->quantity_shipped;

                    // Reduce inventory
                    $this->reduceInventoryForItem($item->product_id, $neededQuantity, $order, $statusHistory);

                    // CRITICAL FIX: Also reduce product stock
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $newStock = $product->stock - $neededQuantity;
                        // Prevent allocated_stock from going negative
                        $newAllocatedStock = max(0, $product->allocated_stock - $neededQuantity);
                        $product->updateStockSilently($newStock, $newAllocatedStock);
                    }

                    $item->quantity_shipped = $item->quantity;
                    $item->save();
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order marked as shipped and inventory updated.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error marking order as shipped: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(Order $order)
    {
        $oldStatus = $order->status;

        if ($oldStatus !== 'shipped') {
            return redirect()->back()->with('error', 'Only shipped orders can be marked as delivered.');
        }

        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        // Record status change
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'delivered',
            'user_id' => Auth::id(),
            'notes' => 'Order marked as delivered'
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order marked as delivered.');
    }

    /**
     * Confirm payment received for an order
     */
    public function confirmPayment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('info', 'Payment has already been confirmed for this order.');
        }

        $oldPaymentStatus = $order->payment_status;
        $order->payment_status = 'paid';
        $order->save();

        // Record status change
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status, // Keep current order status
            'user_id' => Auth::id(),
            'notes' => "Payment confirmed - status changed from {$oldPaymentStatus} to paid"
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment confirmed successfully!');
    }

    /**
     * Ship specific items in an order
     */
    public function shipItems(Request $request, Order $order)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity_shipped' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Record order status history
            $statusHistory = OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'user_id' => Auth::id(),
                'notes' => 'Partial shipment processed'
            ]);

            foreach ($request->items as $shippedItem) {
                $orderItem = $order->items()->findOrFail($shippedItem['order_item_id']);

                $remainingToShip = $orderItem->quantity - ($orderItem->quantity_shipped ?? 0);

                if ($shippedItem['quantity_shipped'] > $remainingToShip) {
                    throw new \Exception("Cannot ship more than the remaining quantity for {$orderItem->product_name}");
                }

                $orderItem->quantity_shipped = ($orderItem->quantity_shipped ?? 0) + $shippedItem['quantity_shipped'];
                $orderItem->save();

                $this->reduceInventoryForItem($orderItem->product_id, $shippedItem['quantity_shipped'], $order, $statusHistory);
            }

            // Check if all items are now shipped
            $allShipped = $order->items()
                ->whereRaw('quantity > COALESCE(quantity_shipped, 0)')
                ->count() === 0;

            if ($allShipped && $order->status !== 'shipped') {
                $order->status = 'shipped';
                $order->delivered_at = now();
                $order->save();

                // Record status change
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'shipped',
                    'user_id' => Auth::id(),
                    'notes' => 'All items shipped'
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Items marked as shipped and inventory updated');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error processing partial shipment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process shipment: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to reduce inventory for a product
     */
    private function reduceInventoryForItem($productId, $quantity, $order, $statusHistory = null)
    {
        $inventoryItems = Inventory::where('product_id', $productId)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->orderBy('expiration_date')
            ->get();

        $remainingToReduce = $quantity;

        foreach ($inventoryItems as $inventory) {
            if ($remainingToReduce <= 0)
                break;

            $reduceAmount = min($inventory->quantity, $remainingToReduce);
            $remainingToReduce -= $reduceAmount;

            $adjustment = InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'decrease',
                'quantity_change' => -$reduceAmount,
                'reason' => "Order shipment: {$order->order_number}",
                'notes' => "Automatic reduction for order item",
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'System',
                'status_history_id' => $statusHistory ? $statusHistory->id : null
            ]);

            $inventory->quantity -= $reduceAmount;

            if ($inventory->quantity <= 0) {
                $inventory->status = 'depleted';
            }

            $inventory->save();
        }

        if ($remainingToReduce > 0) {
            throw new \Exception("Insufficient inventory for product ID {$productId}");
        }
    }

    private function restoreInventoryForItem($productId, $quantity, $order, $statusHistory = null)
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where(function ($query) {
                $query->where('status', 'available')
                    ->orWhere('status', 'depleted');
            })
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$inventory) {
            $product = Product::findOrFail($productId);
            $inventory = Inventory::create([
                'product_id' => $productId,
                'product_name' => $product->name,
                'quantity' => 0,
                'status' => 'available',
                'location' => 'Returned stock',
            ]);
        }

        $adjustment = InventoryAdjustment::create([
            'inventory_id' => $inventory->id,
            'adjustment_type' => 'increase',
            'quantity_change' => $quantity,
            'reason' => "Order cancelled: {$order->order_number}",
            'notes' => "Automatic adjustment - stock restored for cancelled order",
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'System',
            'status_history_id' => $statusHistory ? $statusHistory->id : null
        ]);

        $inventory->quantity += $quantity;

        if ($inventory->status === 'depleted') {
            $inventory->status = 'available';
        }

        $inventory->save();

        // CRITICAL FIX: Also restore product stock when inventory is restored
        $product = Product::findOrFail($productId);
        $newStock = $product->stock + $quantity;
        $product->updateStockSilently($newStock);

        return $adjustment;
    }

    public function history(Order $order)
    {
        // Authorize view permission
        $this->authorizeView($order);

        $statusHistory = $order->statusHistory()
            ->with(['user', 'inventoryAdjustments.inventory'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.history', compact('order', 'statusHistory'));
    }

    private function processShipment(Order $order, OrderStatusHistory $statusHistory)
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            $neededQuantity = $item->quantity - ($item->quantity_shipped ?? 0);
            if ($neededQuantity <= 0) continue;

            // Reduce inventory first
            $this->reduceInventoryForItem($product->id, $neededQuantity, $order, $statusHistory);

            // CRITICAL FIX: Also reduce the product stock
            $newStock = $product->stock - $neededQuantity;
            $newAllocatedStock = $product->allocated_stock - $neededQuantity;
            $product->updateStockSilently($newStock, $newAllocatedStock);

            $item->quantity_shipped = $item->quantity;
            $item->save();
        }

        if (!$order->shipped_at) {
            $order->shipped_at = now();
            $order->save();
        }

        // Create outgoing shipment record for tracking
        $this->createOrderShipment($order);
    }

    /**
     * Create an outgoing shipment record when order is shipped
     */
    private function createOrderShipment(Order $order)
    {
        // Check if shipment already exists for this order
        $existingShipment = Shipment::where('order_id', $order->id)->first();

        if (!$existingShipment) {
            Shipment::create([
                'order_id' => $order->id,
                'status' => 'shipped',
                'shipped_at' => now(),
                'expected_delivery' => now()->addDays(3), // Default 3 days
                'notes' => "Shipment created for order #{$order->order_number}",
            ]);
        }
    }

    private function processCancellation(Order $order, string $oldStatus, Request $request, OrderStatusHistory $statusHistory)
    {
        // 1. Cancel payment status
        if ($order->payment_status !== 'cancelled') {
            $order->payment_status = 'cancelled';
            Log::info("Payment status changed to cancelled for order #{$order->order_number}");
        }

        // 2. Cancel any shipments tied to this order
        $shipments = $order->shipments()->whereNotIn('status', ['cancelled', 'delivered'])->get();
        foreach ($shipments as $shipment) {
            $shipment->status = 'cancelled';
            $shipment->save();
            Log::info("Shipment #{$shipment->shipment_number} cancelled due to order #{$order->order_number} cancellation");
        }

        // 3. Handle inventory restoration for different order statuses
        if (in_array($oldStatus, ['pending', 'processing'])) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    // Prevent allocated_stock from going negative
                    $product->allocated_stock = max(0, $product->allocated_stock - $item->quantity);
                    $product->save();
                }
            }
        }

        if (in_array($oldStatus, ['shipped', 'delivered'])) {
            // Check if user is admin and restore_inventory is checked
            $user = Auth::user();
            if ($user && $user->role === 'admin' && $request->input('restore_inventory')) {
                foreach ($order->items as $item) {
                    if ($item->quantity_shipped > 0) {
                        $this->restoreInventoryForItem($item->product_id, $item->quantity_shipped, $order, $statusHistory);

                        $item->quantity_shipped = 0;
                        $item->save();
                    }
                }
            }
        }
    }
}
