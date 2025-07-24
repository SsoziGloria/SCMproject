@extends('admin.app')

@section('content')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-primary fw-bold">Analytics Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics.revenue') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> Revenue Details
            </a>
            <a href="{{ route('analytics.products') }}" class="btn btn-outline-success">
                <i class="bi bi-box-seam"></i> Product Analytics
            </a>
            <a href="{{ route('analytics.users') }}" class="btn btn-outline-info">
                <i class="bi bi-people"></i> User Analytics
            </a>
        </div>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="section analytics">

        <!-- Enhanced Metrics Cards Row -->
        <div class="row mb-4">
            <!-- Total Orders Card -->
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Orders</h6>
                                <h3 class="fw-bold text-primary mb-2">{{ number_format($analytics['total_orders']) }}</h3>
                                <small class="text-muted">
                                    @if($analytics['orders_growth'] > 0)
                                        <span class="text-success me-1">
                                            <i class="bi bi-arrow-up"></i> +{{ $analytics['orders_growth'] }}%
                                        </span>
                                    @elseif($analytics['orders_growth'] < 0)
                                        <span class="text-danger me-1">
                                            <i class="bi bi-arrow-down"></i> {{ $analytics['orders_growth'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted me-1">
                                            <i class="bi bi-dash"></i> 0%
                                        </span>
                                    @endif
                                    vs last month
                                </small>
                            </div>
                            <div class="card-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-cart text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Revenue</h6>
                                <h3 class="fw-bold text-success mb-2">UGX {{ number_format($analytics['total_revenue']) }}</h3>
                                <small class="text-muted">
                                    @if($analytics['revenue_growth'] > 0)
                                        <span class="text-success me-1">
                                            <i class="bi bi-arrow-up"></i> +{{ $analytics['revenue_growth'] }}%
                                        </span>
                                    @elseif($analytics['revenue_growth'] < 0)
                                        <span class="text-danger me-1">
                                            <i class="bi bi-arrow-down"></i> {{ $analytics['revenue_growth'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted me-1">
                                            <i class="bi bi-dash"></i> 0%
                                        </span>
                                    @endif
                                    vs last month
                                </small>
                            </div>
                            <div class="card-icon bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-currency-dollar text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Active Users</h6>
                                <h3 class="fw-bold text-info mb-2">{{ number_format($analytics['active_users']) }}</h3>
                                <small class="text-muted">
                                    @if($analytics['users_growth'] > 0)
                                        <span class="text-success me-1">
                                            <i class="bi bi-arrow-up"></i> +{{ $analytics['users_growth'] }}%
                                        </span>
                                    @elseif($analytics['users_growth'] < 0)
                                        <span class="text-danger me-1">
                                            <i class="bi bi-arrow-down"></i> {{ $analytics['users_growth'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted me-1">
                                            <i class="bi bi-dash"></i> 0%
                                        </span>
                                    @endif
                                    vs last month
                                </small>
                            </div>
                            <div class="card-icon bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-people text-info fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Products</h6>
                                <h3 class="fw-bold text-warning mb-2">{{ number_format($analytics['total_products']) }}</h3>
                                <small class="text-muted">
                                    @if($analytics['products_growth'] > 0)
                                        <span class="text-success me-1">
                                            <i class="bi bi-arrow-up"></i> +{{ $analytics['products_growth'] }}%
                                        </span>
                                    @elseif($analytics['products_growth'] < 0)
                                        <span class="text-danger me-1">
                                            <i class="bi bi-arrow-down"></i> {{ $analytics['products_growth'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted me-1">
                                            <i class="bi bi-dash"></i> 0%
                                        </span>
                                    @endif
                                    vs last month
                                </small>
                            </div>
                            <div class="card-icon bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-box-seam text-warning fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Revenue Trends</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Order Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="orderStatusChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Movement Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-arrow-up-down me-2"></i>Inventory Movement (Last 14 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="inventoryMovementChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-star me-2"></i>Top Performing Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Qty Sold</th>
                                        <th>Revenue</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['top_products'] as $product)
                                        <tr>
                                            <td class="ps-3">
                                                <div>
                                                    <strong class="text-primary">{{ $product->product_name }}</strong>
                                                    <br><small class="text-muted">ID: {{ $product->product_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($product->total_revenue ?? 0) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->inventory_level > 10 ? 'success' : ($product->inventory_level > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->inventory_level }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No product data available</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Orders</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Order #</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['recent_orders'] as $order)
                                        <tr>
                                            <td class="ps-3">
                                                <a href="{{ route('orders.show', $order->id) }}" class="text-primary fw-bold">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <strong>{{ $order->user->name ?? 'Guest' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $order->total_items }} ({{ $order->items_count }} products)</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($order->total_amount) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $order->status === 'pending' ? 'warning' :
                                                    ($order->status === 'processing' ? 'info' :
                                                        ($order->status === 'shipped' ? 'primary' :
                                                            ($order->status === 'delivered' ? 'success' : 'danger')))
                                                }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-cart text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No recent orders</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Analytics Row -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-people me-2"></i>User Role Distribution</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Role</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                        <th>Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['user_roles'] as $role)
                                        <tr>
                                            <td class="ps-3">
                                                <i class="bi bi-person-{{ $role->role === 'admin' ? 'gear' : ($role->role === 'manager' ? 'check-square' : 'circle') }} me-2 text-muted"></i>
                                                <strong>{{ ucfirst($role->role) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $role->count }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: {{ $role->percentage }}%" 
                                                         aria-valuenow="{{ $role->percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ number_format($role->percentage, 1) }}%</small>
                                            </td>
                                            <td>
                                                @if(isset($role->growth) && $role->growth > 0)
                                                    <span class="text-success">
                                                        <i class="bi bi-arrow-up"></i> {{ $role->growth }}%
                                                    </span>
                                                @elseif(isset($role->growth) && $role->growth < 0)
                                                    <span class="text-danger">
                                                        <i class="bi bi-arrow-down"></i> {{ abs($role->growth) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="bi bi-dash"></i> 0%
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No user data available</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Low Inventory Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Movement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['inventory_status'] as $inventory)
                                        <tr>
                                            <td class="ps-3">
                                                <div>
                                                    <strong class="text-primary">{{ $inventory->product_name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $inventory->total_quantity <= 5 ? 'danger' : ($inventory->total_quantity <= 10 ? 'warning' : 'success') }}">
                                                    {{ $inventory->total_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $inventory->status === 'Out of Stock' ? 'danger' :
                                                    ($inventory->status === 'Low Stock' ? 'warning' : 'success') 
                                                }}">
                                                    {{ $inventory->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($inventory->days_since_movement !== null)
                                                    <small class="text-muted">{{ $inventory->days_since_movement }}d ago</small>
                                                @else
                                                    <small class="text-muted">N/A</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">All items well stocked</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Recent Adjustments</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Change</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['recent_inventory_adjustments'] as $adjustment)
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-primary">{{ $adjustment->inventory->product_name ?? 'Unknown' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $adjustment->quantity_change >= 0 ? 'success' : 'danger' }}">
                                                    {{ $adjustment->formatted_change ?? ($adjustment->quantity_change > 0 ? '+' : '') . $adjustment->quantity_change }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $adjustment->created_at ? $adjustment->created_at->format('M j, H:i') : 'N/A' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No recent adjustments</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inventory Adjustments -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Inventory Adjustments</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Change</th>
                                        <th>Reason</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['recent_inventory_adjustments'] as $adjustment)
                                        <tr>
                                            <td class="ps-3">
                                                <small class="text-muted">{{ $adjustment->created_at->format('M d, H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ $adjustment->inventory->product_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $adjustment->status_color ?? 'secondary' }}">
                                                    {{ ucfirst($adjustment->adjustment_type) }}
                                                </span>
                                            </td>
                                            <td class="{{ $adjustment->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>{{ $adjustment->formatted_change }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($adjustment->reason, 30) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $adjustment->user_name ?? 'System' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No recent adjustments</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($analytics['revenue_chart']['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($analytics['revenue_chart']['data']),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'UGX' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Order Status Chart
            const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(orderStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($analytics['order_status_chart']['labels']),
                    datasets: [{
                        data: @json($analytics['order_status_chart']['data']),
                        backgroundColor: @json($analytics['order_status_chart']['colors'] ?? [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56'
                        ])
                    }]
                }
            });

            // Inventory Movement Chart
            const inventoryMovementCtx = document.getElementById('inventoryMovementChart').getContext('2d');
            new Chart(inventoryMovementCtx, {
                type: 'bar',
                data: {
                    labels: @json($analytics['inventory_movement_chart']['labels']),
                    datasets: [
                        {
                            label: 'Stock In',
                            data: @json($analytics['inventory_movement_chart']['increases']),
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        },
                        {
                            label: 'Stock Out',
                            data: @json($analytics['inventory_movement_chart']['decreases']),
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgb(255, 99, 132)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    }
                }
            });
        </script>
    @endpush