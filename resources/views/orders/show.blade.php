@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Order #{{ $order->order_number }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->order_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Order
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order Details</h5>
                        <div>
                            <span class="badge bg-{{ 
                                            $order->status === 'pending' ? 'warning' :
        ($order->status === 'processing' ? 'info' :
            ($order->status === 'shipped' ? 'primary' :
                ($order->status === 'delivered' ? 'success' : 'danger'))) 
                                        }}">
                                {{ ucfirst($order->status) }}
                            </span>

                            <!-- Order Status Update Button -->
                            @if(in_array(auth()->user()->role, ['admin', 'retailer']))
                                <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#changeStatusModal">
                                    <i class="bi bi-arrow-right-circle"></i> Update Status
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="text-muted">Order Date:</span>
                                    {{ $order->created_at->format('M d, Y, h:i A') }}
                                </p>
                                <p class="mb-2">
                                    <span class="text-muted">Payment Method:</span>
                                    @if($order->payment === 'mobile_money')
                                        Mobile Money
                                    @elseif ($order->payment === 'bank_transfer')
                                        Bank Transfer
                                    @else
                                        {{ ucfirst($order->payment) }}
                                    @endif
                                </p>
                                <p class="mb-2">
                                    <span class="text-muted">Payment Status:</span>
                                    <span class="badge bg-{{ 
                                                        $order->payment_status === 'pending' ? 'warning' :
        ($order->payment_status === 'paid' ? 'success' : 'danger') 
                                                    }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                                @if($order->referral_source)
                                    <p class="mb-0">
                                        <span class="text-muted">Referral Source:</span>
                                        {{ $order->referral_source }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($order->retailer)
                                    <p class="mb-2">
                                        <span class="text-muted">Retailer:</span>
                                        {{ $order->retailer->store_name }}
                                    </p>
                                @endif
                                <p class="mb-0">
                                    <span class="text-muted">Last Updated:</span>
                                    {{ $order->updated_at->format('M d, Y, h:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Inventory Status Alert -->
                        @if(!$allItemsInStock && in_array($order->status, ['pending', 'processing']))
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Inventory Alert:</strong> Some items in this order have insufficient inventory.
                                Please check the inventory status before shipping.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Ordered</th>
                                        <th class="text-center">Shipped</th>
                                        <th class="text-center">Inventory Status</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                        @if(in_array($order->status, ['pending', 'processing']))
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        @php
                                            $availableQuantity = \App\Models\Inventory::where('product_id', $item->product_id)
                                                ->where('status', 'available')
                                                ->where('quantity', '>', 0)
                                                ->sum('quantity');

                                            $neededQuantity = $item->quantity - ($item->quantity_shipped ?? 0);
                                            $itemInStock = $availableQuantity >= $neededQuantity;

                                            $stockStatus = match (true) {
                                                $availableQuantity >= $neededQuantity => 'success',
                                                $availableQuantity > 0 => 'warning',
                                                default => 'danger'
                                            };

                                            $stockLabel = match (true) {
                                                $availableQuantity >= $neededQuantity => 'In Stock',
                                                $availableQuantity > 0 => 'Partial Stock',
                                                default => 'Out of Stock'
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}" class="rounded me-2"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;">
                                                            <i class="bi bi-box text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->product_name ?? $item->product->name }}</h6>
                                                        <small
                                                            class="text-muted">{{ $item->product_category ?? $item->product->category }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">{{ $item->quantity_shipped ?? 0 }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $stockStatus }}">
                                                    {{ $stockLabel }} ({{ $availableQuantity }})
                                                </span>
                                            </td>
                                            <td class="text-end">UGX {{ number_format($item->price, 0) }}</td>
                                            <td class="text-end">UGX {{ number_format($item->price * $item->quantity, 0) }}</td>

                                            @if(in_array($order->status, ['pending', 'processing']))
                                                <td>
                                                    @if(($item->quantity_shipped ?? 0) < $item->quantity)
                                                        <button type="button"
                                                            class="btn btn-sm {{ $stockStatus === 'danger' ? 'btn-outline-danger disabled' : 'btn-outline-primary' }}"
                                                            {{ $stockStatus === 'danger' ? 'disabled' : '' }} data-bs-toggle="modal"
                                                            data-bs-target="#shipItemModal" data-item-id="{{ $item->id }}"
                                                            data-product-name="{{ $item->product_name ?? $item->product->name }}"
                                                            data-quantity="{{ $item->quantity }}"
                                                            data-shipped="{{ $item->quantity_shipped ?? 0 }}"
                                                            data-available="{{ $availableQuantity }}">
                                                            <i class="bi bi-truck"></i> Ship
                                                        </button>
                                                    @else
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-lg"></i> Shipped
                                                        </span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Subtotal</td>
                                        <td colspan="2" class="text-end">
                                            UGX {{ number_format($order->subtotal ?? $order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}
                                        </td>
                                        @if(in_array($order->status, ['pending', 'processing']))
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Tax</strong></td>
                                        <td colspan="2" class="text-end">
                                            UGX {{ number_format($order->total_amount - $order->shipping_fee - $order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}
                                        </td>
                                        @if(in_array($order->status, ['pending', 'processing']))
                                            <td></td>
                                        @endif
                                    </tr>
                                    @if(isset($order->shipping_fee) && $order->shipping_fee > 0)
                                        <tr>
                                            <td colspan="4" class="text-end">Shipping</td>
                                            <td colspan="2" class="text-end">UGX {{ number_format($order->shipping_fee, 0) }}
                                            </td>@if(in_array($order->status, ['pending', 'processing']))
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endif
                                    @if(isset($order->discount_amount) && $order->discount_amount > 0)
                                        <tr>
                                            <td colspan="4" class="text-end">Discount</td>
                                            <td colspan="2" class="text-end">-{{ number_format($order->discount_amount, 2) }}
                                                UGX</td>
                                            @if(in_array($order->status, ['pending', 'processing']))
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total</td>
                                        <td colspan="2" class="text-end fw-bold">UGX
                                            {{ number_format($order->total_amount, 0) }}
                                        </td>
                                        @if(in_array($order->status, ['pending', 'processing']))
                                            <td></td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($order->notes)
                            <div class="mt-3">
                                <h6>Order Notes:</h6>
                                <p class="bg-light p-3 rounded">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.mark-as-shipped', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" {{ $allItemsInStock ? '' : 'disabled' }} {{ $allItemsInStock ? '' : 'title=Some items are out of stock' }}>
                                        <i class="bi bi-truck"></i> Mark Entire Order as Shipped
                                    </button>
                                </form>
                            @endif

                            @if($order->status == 'shipped')
                                <form action="{{ route('orders.mark-as-delivered', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info text-white">
                                        <i class="bi bi-check2-circle"></i> Mark as Delivered
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="modal"
                                data-bs-target="#changeStatusModal">
                                <i class="bi bi-arrow-right-circle"></i> Update Status
                            </button>
                        </div>

                        @if(!$allItemsInStock && in_array($order->status, ['pending', 'processing']))
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                You can still ship individual items that are in stock using the "Ship" button next to each
                                available item.
                            </div>
                        @endif
                    </div>
                </div>

                @if(auth()->user()->role === 'admin')
                    <!-- Admin Actions -->
                    <a href="{{ route('orders.history', $order->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history"></i> View History
                    </a>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Admin Actions</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('orders.update', $order->id) }}" method="POST" class="row">
                                @csrf
                                @method('PUT')
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">Order Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                            Processing</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped
                                        </option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered
                                        </option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled
                                        </option>
                                    </select>
                                    @if(in_array($order->status, ['pending', 'processing']) && !$allItemsInStock)
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            Changing to shipped will reduce inventory levels. Some items are out of stock.
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed
                                        </option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>
                                            Refunded</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Customer Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2">{{ $username }}</h6>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i> {{ $userEmail }}</p>
                        <p><i class="bi bi-telephone me-2"></i> {{ $order->phone }}</p>

                        <hr>

                        <h6>Billing Address</h6>
                        <address class="mb-4">
                            {{ $order->address }}
                        </address>

                        <h6>Shipping Address</h6>
                        <address>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }},
                            @if($order->shipping_region)
                                {{ $order->shipping_region }}<br>
                            @endif
                            {{ $order->shipping_country }}
                        </address>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Timeline</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Order Created</h6>
                                    <small>{{ $order->created_at->format('M d, h:i A') }}</small>
                                </div>
                                <p class="mb-1">Order #{{ $order->order_number }} was created.</p>
                            </div>

                            @if($order->payment_status === 'paid')
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Payment Received</h6>
                                        <small>{{ $order->updated_at->format('M d, h:i A') }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Payment received via
                                        @if($order->payment === 'mobile_money')
                                            Mobile Money.
                                        @elseif ($order->payment === 'bank_transfer')
                                            Bank Transfer.
                                        @else
                                            {{ ucfirst($order->payment) }}
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($order->status === 'processing')
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Processing Started</h6>
                                        <small>{{ $order->updated_at->format('M d, h:i A') }}</small>
                                    </div>
                                    <p class="mb-1">Order is being processed.</p>
                                </div>
                            @endif

                            @if($order->status === 'shipped')
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order Shipped</h6>
                                        <small>{{ $order->updated_at->format('M d, h:i A') }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Order has been shipped.
                                        @if($order->tracking_number)
                                            <br>Tracking #: {{ $order->tracking_number }}
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($order->status === 'delivered')
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order Delivered</h6>
                                        <small>{{ $order->updated_at->format('M d, h:i A') }}</small>
                                    </div>
                                    <p class="mb-1">Order has been delivered to the customer.</p>
                                </div>
                            @endif

                            @if($order->status === 'cancelled')
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order Cancelled</h6>
                                        <small>{{ $order->updated_at->format('M d, h:i A') }}</small>
                                    </div>
                                    <p class="mb-1">Order has been cancelled.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inventory Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Inventory Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Stock Status:</span>
                            <span class="badge bg-{{ $allItemsInStock ? 'success' : 'warning' }} p-2">
                                {{ $allItemsInStock ? 'All Items In Stock' : 'Some Items Low/Out of Stock' }}
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Needed</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        @php
                                            $availableQuantity = \App\Models\Inventory::where('product_id', $item->product_id)
                                                ->where('status', 'available')
                                                ->where('quantity', '>', 0)
                                                ->sum('quantity');

                                            $neededQuantity = $item->quantity - ($item->quantity_shipped ?? 0);

                                            $stockStatus = match (true) {
                                                $availableQuantity >= $neededQuantity => 'success',
                                                $availableQuantity > 0 => 'warning',
                                                default => 'danger'
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $item->product_name ?? $item->product->name }}</td>
                                            <td>{{ $neededQuantity }}</td>
                                            <td>{{ $availableQuantity }}</td>
                                            <td>
                                                <span class="badge bg-{{ $stockStatus }}">
                                                    {{ $availableQuantity >= $neededQuantity ? 'OK' : ($availableQuantity > 0 ? 'Low' : 'Out') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeStatusModalLabel">Change Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Changing order status to "shipped" or "delivered" will automatically update inventory levels.
                        </div>

                        @if(!$allItemsInStock)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Warning:</strong> Some items in this order have insufficient inventory.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="status" class="form-label">New Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status_notes" class="form-label">Status Notes (Optional)</label>
                            <textarea class="form-control" id="status_notes" name="status_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ship Item Modal -->
    <div class="modal fade" id="shipItemModal" tabindex="-1" aria-labelledby="shipItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.ship-items', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="shipItemModalLabel">Ship Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="items[0][order_item_id]" id="modalItemId">

                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" id="modalProductName" readonly>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Ordered</label>
                                <input type="text" class="form-control" id="modalQuantity" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Already Shipped</label>
                                <input type="text" class="form-control" id="modalShipped" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Available Stock</label>
                                <input type="text" class="form-control" id="modalAvailable" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantityToShip" class="form-label">Quantity to Ship Now</label>
                            <input type="number" class="form-control" id="quantityToShip" name="items[0][quantity_shipped]"
                                min="1" required>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            This action will reduce inventory levels accordingly.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Ship Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize ship item modal
            document.addEventListener('DOMContentLoaded', function () {
                const shipItemModal = document.getElementById('shipItemModal');
                if (shipItemModal) {
                    shipItemModal.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        const itemId = button.getAttribute('data-item-id');
                        const productName = button.getAttribute('data-product-name');
                        const quantity = button.getAttribute('data-quantity');
                        const shipped = button.getAttribute('data-shipped');
                        const available = button.getAttribute('data-available');
                        const remaining = quantity - shipped;

                        document.getElementById('modalItemId').value = itemId;
                        document.getElementById('modalProductName').value = productName;
                        document.getElementById('modalQuantity').value = quantity;
                        document.getElementById('modalShipped').value = shipped;
                        document.getElementById('modalAvailable').value = available;

                        const quantityToShip = Math.min(remaining, available);
                        document.getElementById('quantityToShip').value = quantityToShip;
                        document.getElementById('quantityToShip').max = quantityToShip;
                    });
                }
            });
        </script>
    @endpush
@endsection