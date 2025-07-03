{{-- filepath: resources/views/orders/show.blade.php --}}
@extends('user.app')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Order Details</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(isset($order))
            <div class="row">
                <!-- Order Summary -->
                <div class="col-lg-8">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                            <span class="badge 
                                        @if($order->status === 'pending') bg-warning
                                        @elseif($order->status === 'processing') bg-info
                                        @elseif($order->status === 'shipped') bg-primary
                                        @elseif($order->status === 'delivered') bg-success
                                        @elseif($order->status === 'cancelled') bg-danger
                                            @else bg-secondary
                                        @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Order Date</p>
                                    <p class="fw-bold">{{ $order->created_at->format('M d, Y, h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Payment Method</p>
                                    <p class="fw-bold">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment ?? 'Not specified')) }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Payment Status</p>
                                    <p>
                                        @if($order->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($order->payment_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Delivery Status</p>
                                    <p>
                                        @if($order->delivered_at)
                                            <span class="badge bg-success">Delivered on
                                                {{ \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') }}</span>
                                        @else
                                            <span class="badge bg-secondary">Not delivered yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php $product = $item->product @endphp
                                                        @if($product && $product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                                alt="{{ $product->name }}" class="rounded me-3"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3" style="width:50px;height:50px;"></div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $product->name ?? 'Product #' . $item->product_id }}
                                                            </h6>
                                                            @if($product && $product->category)
                                                                <small class="text-muted">{{ $product->category }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">UGX {{ number_format($item->price, 0) }}</td>
                                                <td class="text-end">UGX {{ number_format($item->price * $item->quantity, 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">No items found for this order.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end">UGX {{ number_format($order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                            <td class="text-end">UGX {{ number_format($order->total_amount - $order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>UGX
                                                    {{ number_format($order->total_amount + ($order->shipping_cost ?? 0), 0) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Order Placed</h6>
                                        <p class="text-muted">{{ $order->created_at->format('M d, Y, h:i A') }}</p>
                                    </div>
                                </div>

                                @if($order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <h6>Processing</h6>
                                            <p class="text-muted">Order confirmed and is being processed</p>
                                        </div>
                                    </div>
                                @endif

                                @if($order->status == 'shipped' || $order->status == 'delivered')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6>Shipped</h6>
                                            <p class="text-muted">Your order is on its way</p>
                                        </div>
                                    </div>
                                @endif

                                @if($order->delivered_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6>Delivered</h6>
                                            <p class="text-muted">
                                                {{ \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($order->status == 'cancelled')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-danger"></div>
                                        <div class="timeline-content">
                                            <h6>Cancelled</h6>
                                            <p class="text-muted">Order has been cancelled</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="col-lg-4">
                    <!-- Customer Information -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $order->phone ?? 'N/A' }}</p>
                            @if(Auth::user()->role === 'admin')
                                <hr>
                                <a href="{{ route('admin.users.show', $order->user_id ?? 0) }}"
                                    class="btn btn-sm btn-outline-primary">View Customer Profile</a>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <address class="mb-0">
                                <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                                {{ $order->shipping_address ?? 'N/A' }}<br>
                                {{ $order->shipping_city ?? 'N/A' }}, {{ $order->shipping_state ?? '' }}
                                {{ $order->shipping_zipcode ?? '' }}<br>
                                {{ $order->shipping_country ?? 'N/A' }}
                            </address>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Actions</h5>
                        </div>
                        <div class="card-body">
                            @if(Auth::user()->role === 'admin')
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="mb-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group">
                                        <select name="status" class="form-select">
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
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            @endif

                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary"> <!-- { route('invoices.show', $order->id) }} -->
                                    <i class="bi bi-file-earmark-text"></i> View Invoice
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="bi bi-printer"></i> Print Order
                                </button>
                                @if(Auth::user()->role === 'admin')
                                    <a href="#" class="btn btn-outline-warning">
                                        <!-- { route('admin.orders.edit', $order->id) }} -->
                                        <i class="bi bi-pencil"></i> Edit Order
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    @if($order->notes)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Notes</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Order not found.
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">View All Orders</a>
        @endif
    </div>

    <!-- CSS for timeline -->
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            width: 15px;
            height: 15px;
            left: -30px;
            border-radius: 50%;
        }

        .timeline-item:not(:last-child):after {
            content: '';
            position: absolute;
            left: -23px;
            top: 15px;
            height: calc(100% - 15px);
            width: 2px;
            background: #e9ecef;
        }
    </style>
@endsection