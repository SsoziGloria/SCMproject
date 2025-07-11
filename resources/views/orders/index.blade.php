@php
    if (Auth::check() && Auth::user()->role === 'admin') {
        $layout = 'admin.app';
    } elseif (Auth::check() && Auth::user()->role === 'retailer') {
        $layout = 'retailer.app';
    } elseif (Auth::check() && Auth::user()->role === 'supplier') {
        $layout = 'supplier.app';
    } else {
        $layout = 'user.app';
    }
@endphp

@extends($layout)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                @if(Auth::user()->role === 'admin')
                    All Orders
                @elseif(Auth::user()->role === 'retailer')
                    All Shop Orders
                @elseif(Auth::user()->role === 'supplier')
                    Orders for Your Products
                @else
                    My Orders
                @endif
            </h2>

            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'retailer')
                <div>
                    <a href="{{ route('orders.export') }}" class="btn btn-outline-success me-2">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Order
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <!-- Filters -->
                <div class="p-3 bg-light border-bottom">
                    <form action="{{ route('orders.index') }}" method="GET" class="row row-cols-1 row-cols-md-4 g-2">
                        <div class="col">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Status</span>
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped
                                    </option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Sort</span>
                                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First
                                    </option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                    </option>
                                    <option value="total_high" {{ request('sort') == 'total_high' ? 'selected' : '' }}>Total
                                        (High to Low)</option>
                                    <option value="total_low" {{ request('sort') == 'total_low' ? 'selected' : '' }}>Total
                                        (Low to High)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input-group input-group-sm">
                                <input type="date" class="form-control form-control-sm" name="date_from"
                                    value="{{ request('date_from') }}" placeholder="Date From">
                                <input type="date" class="form-control form-control-sm" name="date_to"
                                    value="{{ request('date_to') }}" placeholder="Date To">
                            </div>
                        </div>

                        <div class="col">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" name="search"
                                    value="{{ request('search') }}" placeholder="Search orders...">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Products</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $order->order_number }}</span>
                                        @if(Auth::user()->role === 'admin')
                                            <div class="small text-muted">
                                                {{ $order->user->name ?? 'Guest' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php $items = $order->items; @endphp
                                        @if($items->isNotEmpty())
                                            <div class="d-flex align-items-center">
                                                @php $product = $items->first()->product; @endphp
                                                @if($product && $product->image)
                                                    <div class="flex-shrink-0 me-2">
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                            class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <div>{{ $product->name ?? 'Product #' . $items->first()->product_id }}</div>
                                                    <div class="small text-muted">
                                                        {{ $items->first()->quantity }}x
                                                        @if($items->count() > 1)
                                                            + {{ $items->count() - 1 }} more item(s)
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No items</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">UGX {{ number_format($order->total_amount, 0) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                                                                                                                        @if($order->status === 'pending') bg-warning
                                                                                                                                        @elseif($order->status === 'processing') bg-info
                                                                                                                                        @elseif($order->status === 'shipped') bg-primary
                                                                                                                                        @elseif($order->status === 'delivered') bg-success
                                                                                                                                        @elseif($order->status === 'cancelled') bg-danger
                                                                                                                                            @else bg-secondary
                                                                                                                                        @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ ucfirst(str_replace('_', ' ', $order->payment ?? 'N/A')) }}</div>
                                        <span
                                            class="badge 
                                                                                                                                        @if($order->payment_status === 'paid') bg-success
                                                                                                                                        @elseif($order->payment_status === 'pending') bg-warning
                                                                                                                                            @else bg-danger
                                                                                                                                        @endif">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('orders.show', $order->id) }}"
                                                class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'retailer' && $order->user_id !== Auth::id() && $order->status !== 'cancelled') || (Auth::user()->role === 'supplier' && $order->items->first()->product->supplier_id === Auth::id()) && $order->status !== 'cancelled')
                                                <a href="{{ route('orders.edit', $order->id) }}"
                                                    class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                                    <form action="{{ route('orders.update', $order->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <input type="hidden" name="payment_status" value="failed">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3">No orders found</h5>
                                            <p class="text-muted">
                                                {{ request()->has('search') ? 'Try a different search term' : 'Orders will appear here once created' }}
                                            </p>
                                            @if(Auth::user()->role !== 'supplier' && Auth::user()->role !== 'admin')
                                                <a href="{{ route('shop.index') }}" class="btn btn-sm btn-primary mt-2">Browse
                                                    Products</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination  -->
                @if($orders->hasPages())
                    <div class="p-3 d-flex justify-content-center">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'retailer')
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Total Orders</h6>
                                    <h3 class="mb-0">{{ $stats['total_orders'] ?? $orders->total() }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cart-check fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Revenue</h6>
                                    <h3 class="mb-0">UGX {{ number_format($stats['total_revenue'] ?? 0, 0) }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash-stack fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Pending Orders</h6>
                                    <h3 class="mb-0">{{ $stats['pending_orders'] ?? 0 }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-hourglass-split fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning text-white shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Shipped Orders</h6>
                                    <h3 class="mb-0">{{ $stats['shipped_orders'] ?? 0 }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-truck fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection