@extends(auth()->user()->role . '.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
        <div>
            <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add New Stock
            </a>
            <a href="{{ route('inventories.export') }}" class="btn btn-outline-success ms-2">
                <i class="bi bi-file-earmark-excel me-1"></i> Export
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold">Inventory List</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('inventories.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                    placeholder="Search products...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status')=='available' ? 'selected' : '' }}>
                                    Available</option>
                                <option value="low_stock" {{ request('status')=='low_stock' ? 'selected' : '' }}>Low
                                    Stock</option>
                                <option value="out_of_stock" {{ request('status')=='out_of_stock' ? 'selected' : '' }}>
                                    Out of Stock</option>
                                <option value="reserved" {{ request('status')=='reserved' ? 'selected' : '' }}>Reserved
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="supplier_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id')==$supplier->
                                    supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="expiration" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Expiration</option>
                                <option value="soon" {{ request('expiration')=='soon' ? 'selected' : '' }}>Expiring Soon
                                </option>
                                <option value="expired" {{ request('expiration')=='expired' ? 'selected' : '' }}>Expired
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>Newest First
                                </option>
                                <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>Oldest First
                                </option>
                                <option value="quantity_asc" {{ request('sort')=='quantity_asc' ? 'selected' : '' }}>
                                    Quantity (Low to High)</option>
                                <option value="quantity_desc" {{ request('sort')=='quantity_desc' ? 'selected' : '' }}>
                                    Quantity (High to Low)</option>
                                <option value="expiry" {{ request('sort')=='expiry' ? 'selected' : '' }}>Expiration Date
                                </option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <a href="{{ route('inventories.index') }}"
                                class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Batch #</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th>Location</th>
                            <th>Expiration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product_name }}" class="me-2"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-2" style="width: 40px; height: 40px;"></div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $item->product_name }}</div>
                                        <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold {{ $item->quantity < 10 ? 'text-danger' : '' }}">
                                    {{ $item->quantity }}
                                </span>
                                <small class="text-muted">{{ $item->unit }}</small>
                            </td>
                            <td>{{ $item->batch_number ?? 'N/A' }}</td>
                            <td>
                                @if($item->status == 'available')
                                <span class="badge bg-success">Available</span>
                                @elseif($item->status == 'low_stock')
                                <span class="badge bg-warning">Low Stock</span>
                                @elseif($item->status == 'out_of_stock')
                                <span class="badge bg-danger">Out of Stock</span>
                                @elseif($item->status == 'reserved')
                                <span class="badge bg-info">Reserved</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $supplier->getNameById($item->supplier_id) ?? 'N/A' }}</td>
                            <td>{{ $item->location ?? 'N/A' }}</td>
                            <td>
                                @if($item->expiration_date)
                                @if($item->expiration_date->isPast())
                                <span class="text-danger">{{ $item->expiration_date->format('M d, Y') }}</span>
                                @elseif($item->expiration_date->diffInDays(now()) < 30) <span class="text-warning">{{
                                    $item->expiration_date->format('M d, Y') }}</span>
                                    @else
                                    {{ $item->expiration_date->format('M d, Y') }}
                                    @endif
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('inventories.edit', $item->id) }}">Edit Stock</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('inventories.history', $item->id) }}">View History</a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('inventories.update-status', $item->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="out_of_stock">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    Mark Out of Stock
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-center py-4">
                                    <img src="{{ asset('images/empty-inventory.svg') }}" alt="No inventory found"
                                        style="max-width: 200px;">
                                    <h5 class="mt-3">No inventory items found</h5>
                                    <p class="text-muted">Add your first inventory item to get started</p>
                                    <a href="{{ route('inventories.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Add New Stock
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $inventory->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Pending Orders Affecting Inventory -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Pending Orders Affecting Inventory</h6>
                </div>
                <div class="card-body">
                    @if($pendingOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="popover"
                                            data-bs-html="true" data-bs-trigger="focus" data-bs-title="Order Items"
                                            data-bs-content="{{ $order->products_tooltip }}">
                                            {{ $order->items->count() }} items
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <p class="mb-0 text-muted">No pending orders affecting inventory</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Inventory Adjustments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Inventory Adjustments</h6>
                    <a href="{{ route('inventories.adjustments') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentAdjustments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Change</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAdjustments as $adjustment)
                                <tr>
                                    <td>{{ $adjustment->created_at->format('M d, H:i') }}</td>
                                    <td>{{ $adjustment->inventory->product_name }}</td>
                                    <td>
                                        @php
                                        $typeClass = match ($adjustment->adjustment_type) {
                                        'increase' => 'success',
                                        'decrease' => 'danger',
                                        'correction' => 'info',
                                        'damage' => 'dark',
                                        'expiry' => 'secondary',
                                        default => 'primary'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $typeClass }}">
                                            {{ ucfirst($adjustment->adjustment_type) }}
                                        </span>
                                    </td>
                                    <td
                                        class="{{ $adjustment->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $adjustment->quantity_change >= 0 ? '+' : '' }}{{
                                        $adjustment->quantity_change }}
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $adjustment->reason }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <p class="mb-0 text-muted">No recent inventory adjustments</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase small mb-1">Total Stock Items</div>
                        <div class="h4 mb-0 fw-bold">{{ number_format($stats['total_items']) }}</div>
                    </div>
                    <span class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="bi bi-boxes fs-2"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase small mb-1">Products Stocked</div>
                        <div class="h4 mb-0 fw-bold">{{ $stats['product_count'] }}</div>
                    </div>
                    <span class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="bi bi-box-seam fs-2"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase small mb-1">Low Stock Items</div>
                        <div class="h4 mb-0 fw-bold">{{ $stats['low_stock_count'] }}</div>
                    </div>
                    <span class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </span>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between px-3">
                    <a class="small text-warning fw-semibold text-decoration-none"
                        href="{{ route('inventories.index', ['status' => 'low_stock']) }}">View Details</a>
                    <i class="bi bi-chevron-right text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase small mb-1">Expiring Soon</div>
                        <div class="h4 mb-0 fw-bold">{{ $stats['expiring_soon_count'] }}</div>
                    </div>
                    <span class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                        <i class="bi bi-calendar-x fs-2"></i>
                    </span>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between px-3">
                    <a class="small text-danger fw-semibold text-decoration-none"
                        href="{{ route('inventories.index', ['expiration' => 'soon']) }}">View Details</a>
                    <i class="bi bi-chevron-right text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection