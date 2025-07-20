@extends(auth()->user()->role . '.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Analytics Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Analytics</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal mb-2">Total Orders</h6>
                            <h3 class="mb-2">{{ $analytics['total_orders'] }}</h3>
                            <p class="mb-0 text-muted small">
                                @if($analytics['orders_growth'] > 0)
                                    <span class="text-success me-2">
                                        <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['orders_growth'] }}%
                                    </span>
                                @elseif($analytics['orders_growth'] < 0)
                                    <span class="text-danger me-2">
                                        <i class="mdi mdi-arrow-down-bold"></i> {{ abs($analytics['orders_growth']) }}%
                                    </span>
                                @else
                                    <span class="text-muted me-2">
                                        <i class="mdi mdi-minus"></i> {{ $analytics['orders_growth'] }}%
                                    </span>
                                @endif
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                        <div class="ms-3">
                            <i class="mdi mdi-cart-outline h1 text-primary" style="font-size:2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal mb-2">Total Revenue</h6>
                            <h3 class="mb-2">UGX {{ number_format($analytics['total_revenue'], 0) }}</h3>
                            <p class="mb-0 text-muted small">
                                @if($analytics['revenue_growth'] > 0)
                                    <span class="text-success me-2">
                                        <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['revenue_growth'] }}%
                                    </span>
                                @elseif($analytics['revenue_growth'] < 0)
                                    <span class="text-danger me-2">
                                        <i class="mdi mdi-arrow-down-bold"></i> {{ abs($analytics['revenue_growth']) }}%
                                    </span>
                                @else
                                    <span class="text-muted me-2">
                                        <i class="mdi mdi-minus"></i> {{ $analytics['revenue_growth'] }}%
                                    </span>
                                @endif
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                        <div class="ms-3">
                            <i class="mdi mdi-currency-usd h1 text-success" style="font-size:2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal mb-2">Active Users</h6>
                            <h3 class="mb-2">{{ $analytics['active_users'] }}</h3>
                            <p class="mb-0 text-muted small">
                                @if($analytics['users_growth'] > 0)
                                    <span class="text-success me-2">
                                        <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['users_growth'] }}%
                                    </span>
                                @elseif($analytics['users_growth'] < 0)
                                    <span class="text-danger me-2">
                                        <i class="mdi mdi-arrow-down-bold"></i> {{ abs($analytics['users_growth']) }}%
                                    </span>
                                @else
                                    <span class="text-muted me-2">
                                        <i class="mdi mdi-minus"></i> {{ $analytics['users_growth'] }}%
                                    </span>
                                @endif
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                        <div class="ms-3">
                            <i class="mdi mdi-account-multiple h1 text-info" style="font-size:2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal mb-2">Total Products</h6>
                            <h3 class="mb-2">{{ $analytics['total_products'] }}</h3>
                            <p class="mb-0 text-muted small">
                                @if($analytics['products_growth'] > 0)
                                    <span class="text-success me-2">
                                        <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['products_growth'] }}%
                                    </span>
                                @elseif($analytics['products_growth'] < 0)
                                    <span class="text-danger me-2">
                                        <i class="mdi mdi-arrow-down-bold"></i> {{ abs($analytics['products_growth']) }}%
                                    </span>
                                @else
                                    <span class="text-muted me-2">
                                        <i class="mdi mdi-minus"></i> {{ $analytics['products_growth'] }}%
                                    </span>
                                @endif
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                        <div class="ms-3">
                            <i class="mdi mdi-package-variant h1 text-warning" style="font-size:2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Revenue Trends</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Status Distribution</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Movement Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Inventory Movement (Last 14 Days)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="inventoryMovementChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Top Performing Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity Sold</th>
                                        <th>Revenue</th>
                                        <th>Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['top_products'] as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $product->product_name }}</h6>
                                                        <small class="text-muted">ID: {{ $product->product_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->total_quantity }}</td>
                                            <td>UGX {{ number_format($product->total_revenue ?? 0, 0) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $product->inventory_level > 10 ? 'success' : ($product->inventory_level > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->inventory_level }}
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

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['recent_orders'] as $order)
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary">
                                                                            {{ $order->order_number }}
                                                                        </a>
                                                                    </td>
                                                                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                                    <td>{{ $order->total_items }} ({{ $order->items_count }} products)</td>
                                                                    <td>UGX {{ number_format($order->total_amount, 0) }}</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-{{ 
                                                                                                                                                                                                                                                                                                                                                    $order->status === 'pending' ? 'warning' :
                                        ($order->status === 'processing' ? 'info' :
                                            ($order->status === 'shipped' ? 'primary' :
                                                ($order->status === 'delivered' ? 'success' : 'danger')))
                                                                                                                                                                                                                                                                                                                                                }}">
                                                                            {{ ucfirst($order->status) }}
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

        <!-- User Analytics Row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">User Role Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                        <th>Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['user_roles'] as $role)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($role->role) }}</span>
                                            </td>
                                            <td>{{ $role->count }}</td>
                                            <td>{{ number_format($role->percentage, 1) }}%</td>
                                            <td>
                                                @if($role->growth > 0)
                                                    <span class="text-success">
                                                        <i class="mdi mdi-arrow-up"></i> {{ $role->growth }}%
                                                    </span>
                                                @elseif($role->growth < 0)
                                                    <span class="text-danger">
                                                        <i class="mdi mdi-arrow-down"></i> {{ abs($role->growth) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="mdi mdi-minus"></i> 0%
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Low Inventory Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th>Last Movement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['inventory_status'] as $inventory)
                                                                <tr>
                                                                    <td>{{ $inventory->product_name }}</td>
                                                                    <td>{{ $inventory->total_quantity }}</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-{{ 
                                                                                                                                                                                                                                                                                                                                                    $inventory->status === 'Out of Stock' ? 'danger' :
                                        ($inventory->status === 'Low Stock' ? 'warning' : 'success') 
                                                                                                                                                                                                                                                                                                                                                }}">
                                                                            {{ $inventory->status }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        @if($inventory->days_since_movement !== null)
                                                                            {{ $inventory->days_since_movement }} days ago
                                                                        @else
                                                                            <span class="text-muted">N/A</span>
                                                                        @endif
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

        <!-- Recent Inventory Adjustments -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Inventory Adjustments</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Change</th>
                                        <th>Reason</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['recent_inventory_adjustments'] as $adjustment)
                                        <tr>
                                            <td>{{ $adjustment->created_at->format('M d, H:i') }}</td>
                                            <td>{{ $adjustment->inventory->product_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $adjustment->status_color }}">
                                                    {{ ucfirst($adjustment->adjustment_type) }}
                                                </span>
                                            </td>
                                            <td
                                                class="{{ $adjustment->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $adjustment->formatted_change }}
                                            </td>
                                            <td>{{ Str::limit($adjustment->reason, 30) }}</td>
                                            <td>{{ $adjustment->user_name ?? 'System' }}</td>
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
@endsection