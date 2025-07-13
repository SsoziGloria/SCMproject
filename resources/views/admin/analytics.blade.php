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
                                <span class="text-success me-2">
                                    <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['orders_growth'] }}%
                                </span>
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
                                <span class="text-success me-2">
                                    <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['revenue_growth'] }}%
                                </span>
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
                                <span class="text-warning me-2">
                                    <i class="mdi mdi-arrow-down-bold"></i> {{ $analytics['users_growth'] }}%
                                </span>
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
                                <span class="text-success me-2">
                                    <i class="mdi mdi-arrow-up-bold"></i> {{ $analytics['products_growth'] }}%
                                </span>
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
                                        <th>Sales</th>
                                        <th>Revenue</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['top_products'] as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                            alt="{{ $product->name }}" class="rounded me-2"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                                        <small class="text-muted">{{ $product->product_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->total_sales ?? 0 }}</td>
                                            <td>UGX {{ number_format($product->total_revenue ?? 0, 0) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->stock }}
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
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['recent_orders'] as $order)
                                        <tr>
                                            <td>
                                                {{-- <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="text-primary"> --}}
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>UGX{{ number_format($order->total_amount, 0) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
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
                                                <span class="text-success">
                                                    <i class="mdi mdi-arrow-up"></i> {{ $role->growth ?? 0 }}%
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
                        <h4 class="card-title">Inventory Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th>Expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['inventory_status'] as $inventory)
                                        <tr>
                                            <td>{{ $inventory->product_name }}</td>
                                            <td>{{ $inventory->quantity }} {{ $inventory->unit }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $inventory->quantity > 50 ? 'success' : ($inventory->quantity > 10 ? 'warning' : 'danger') }}">
                                                    {{ $inventory->quantity > 50 ? 'High' : ($inventory->quantity > 10 ? 'Low' : 'Critical') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($inventory->expiration_date)
                                                    {{ \Carbon\Carbon::parse($inventory->expiration_date)->format('M d, Y') }}
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
                                    return '$' + value.toLocaleString();
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
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection