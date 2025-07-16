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

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order Details</h5>
                        <span class="badge bg-{{ 
                                                                                            $order->status === 'pending' ? 'warning' :
        ($order->status === 'processing' ? 'info' :
            ($order->status === 'shipped' ? 'primary' :
                ($order->status === 'delivered' ? 'success' : 'danger'))) 
                                                                                        }}">
                            {{ ucfirst($order->status) }}
                        </span>
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

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
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
                                            <td class="text-end">{{ number_format($item->price, 2) }} UGX</td>
                                            <td class="text-end">{{ number_format($item->price * $item->quantity, 2) }} UGX</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                        <td class="text-end">
                                            {{ number_format($order->subtotal ?? $order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 2) }}
                                            UGX
                                        </td>
                                    </tr>
                                    @if(isset($order->shipping_fee) && $order->shipping_fee > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">Shipping</td>
                                            <td class="text-end">{{ number_format($order->shipping_fee, 2) }} UGX</td>
                                        </tr>
                                    @endif
                                    @if(isset($order->discount_amount) && $order->discount_amount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">Discount</td>
                                            <td class="text-end">-{{ number_format($order->discount_amount, 2) }} UGX</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($order->total_amount, 2) }} UGX</td>
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

                @if(auth()->user()->role === 'admin')
                    <!-- Admin Actions -->
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
                <div class="card shadow-sm">
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
            </div>
        </div>
    </div>
@endsection