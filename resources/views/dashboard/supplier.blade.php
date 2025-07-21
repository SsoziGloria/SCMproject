@extends('supplier.app')

@section('content')
<div class="pagetitle">
    <h1>My Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-12 mb-4">
            <h4>Welcome back, {{ Auth::user()->name }}!</h4>
            <p class="text-muted">Here's a summary of your shop's activity.</p>
        </div>
    </div>

    <!-- Dashboard Summary Cards -->
    <div class="row">
        <!-- My Products Card -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="card-title">My Products</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i
                                class="bi bi-box-seam"></i></div>
                        <div class="ps-3">
                            <h6>{{ $stats['totalProducts'] }}</h6>
                            <span class="text-muted small">Total products listed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- My Inventory Value Card -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="card-title">My Inventory Value</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i
                                class="bi bi-cash-coin"></i></div>
                        <div class="ps-3">
                            <h6>UGX {{ number_format($stats['totalInventoryValue']) }}</h6>
                            <span class="text-muted small">Value of all stock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Orders Card -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Pending Orders</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i
                                class="bi bi-cart-check"></i></div>
                        <div class="ps-3">
                            <h6 class="text-primary">{{ $stats['pendingOrders'] }}</h6>
                            <span class="text-muted small">Awaiting fulfillment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Low Stock Card -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Items</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i
                                class="bi bi-exclamation-triangle text-warning"></i></div>
                        <div class="ps-3">
                            <h6 class="text-warning">{{ $stats['lowStockCount'] }}</h6>
                            <span class="text-muted small">Need restocking</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- End Summary Cards -->

    <!-- Main Content Row -->
    <div class="row">
        <!-- Recent Orders List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <th scope="row"><a href="#">#{{ $order->id }}</a></th>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>UGX {{ number_format($order->total_amount) }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($order->status) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock & Quick Actions Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('supplier.products.create') }}" class="btn btn-primary"><i
                                class="bi bi-plus-circle me-2"></i>Add New Product</a>
                        <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary"><i
                                class="bi bi-list-ul me-2"></i>View All My Products</a>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items List -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Low Stock Products</h5>
                    @if($lowStockProducts->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach ($lowStockProducts as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('supplier.products.edit', $item->id) }}">{{ $item->name }}</a>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $item->stock }} left</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-center text-muted py-3">All your products are well-stocked!</p>
                    @endif
                </div>
            </div>
        </div>
    </div><!-- End Main Content Row -->
</section>
@endsection