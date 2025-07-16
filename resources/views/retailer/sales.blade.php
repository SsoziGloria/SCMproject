@extends('retailer.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Sales Overview Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Sales Dashboard</h1>
                <p class="text-muted">Overview of your store's performance</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ $selectedPeriod ?? 'Last 30 days' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('sales', ['period' => 'today']) }}">Today</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('sales', ['period' => 'week']) }}">This
                                Week</a></li>
                        <li><a class="dropdown-item" href="{{ route('sales', ['period' => 'month']) }}">Last 30
                                days</a></li>
                        <li><a class="dropdown-item" href="{{ route('sales', ['period' => 'quarter']) }}">This
                                Quarter</a></li>
                        <li><a class="dropdown-item" href="{{ route('sales', ['period' => 'year']) }}">This
                                Year</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#customDateModal">Custom Range</a></li>
                    </ul>
                </div>
                <a href="{{ route('orders.create') }}" class="btn btn-primary ms-2">
                    <i class="bi bi-plus"></i> New Sale
                </a>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-0">Total Sales</p>
                                <h3 class="mt-2">{{ number_format($metrics['totalSales'], 2) }} UGX</h3>
                                <span class="badge {{ $metrics['salesGrowth'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <i class="bi bi-arrow-{{ $metrics['salesGrowth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($metrics['salesGrowth']) }}%
                                </span>
                                <small class="text-muted ms-1">vs previous period</small>
                            </div>
                            <div class="icon-shape rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-0">Orders</p>
                                <h3 class="mt-2">{{ $metrics['orderCount'] }}</h3>
                                <span class="badge {{ $metrics['orderGrowth'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <i class="bi bi-arrow-{{ $metrics['orderGrowth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($metrics['orderGrowth']) }}%
                                </span>
                                <small class="text-muted ms-1">vs previous period</small>
                            </div>
                            <div class="icon-shape rounded-circle bg-success bg-opacity-10 text-success p-3">
                                <i class="bi bi-cart-check fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-0">Average Order</p>
                                <h3 class="mt-2">{{ number_format($metrics['averageOrder'], 2) }} UGX</h3>
                                <span class="badge {{ $metrics['aovGrowth'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <i class="bi bi-arrow-{{ $metrics['aovGrowth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($metrics['aovGrowth']) }}%
                                </span>
                                <small class="text-muted ms-1">vs previous period</small>
                            </div>
                            <div class="icon-shape rounded-circle bg-info bg-opacity-10 text-info p-3">
                                <i class="bi bi-receipt fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-0">Profit</p>
                                <h3 class="mt-2">{{ number_format($metrics['profit'], 2) }} UGX</h3>
                                <span class="badge {{ $metrics['profitGrowth'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <i class="bi bi-arrow-{{ $metrics['profitGrowth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($metrics['profitGrowth']) }}%
                                </span>
                                <small class="text-muted ms-1">vs previous period</small>
                            </div>
                            <div class="icon-shape rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                                <i class="bi bi-wallet fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Sales Trend Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title mb-0">Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Top Products</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Units</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                            alt="{{ $product->name }}" class="rounded"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;">
                                                            <i class="bi bi-box text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div class="ms-2">
                                                        <p class="mb-0 fw-medium">{{ Str::limit($product->name, 20) }}</p>
                                                        <small class="text-muted">{{ $product->category }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $product->units_sold }}</td>
                                            <td class="text-end">{{ number_format($product->revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">No sales data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Orders</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{ route('retailer.orders.show', $order->id) }}" class="fw-medium">
                                                                            #{{ $order->order_number }}
                                                                        </a>
                                                                    </td>
                                                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                                    <td>
                                                                        @if($order->user)
                                                                            {{ $order->user->name }}
                                                                        @else
                                                                            {{ $order->customer_name ?? 'Walk-in Customer' }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' :
                                        ($order->status == 'pending' ? 'warning' :
                                            ($order->status == 'processing' ? 'info' : 'secondary')) }}">
                                                                            {{ ucfirst($order->status) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-end">{{ number_format($order->total_amount, 2) }} UGX</td>
                                                                </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No recent orders</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Category -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title mb-0">Sales by Category</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Date Range Modal -->
    <div class="modal fade" id="customDateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Date Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sales') }}" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            // new Chart(salesCtx, {
            //     type: 'line',
            //     data: {
            //         labels: {!! json_encode($salesData['labels']) !!},
            //         datasets: [{
            //             label: 'Sales (UGX)',
            //             data: {!! json_encode($salesData['values']) !!},
            //             borderColor: '#5e72e4',
            //             backgroundColor: 'rgba(94, 114, 228, 0.1)',
            //             borderWidth: 2,
            //             fill: true,
            //             tension: 0.4
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         maintainAspectRatio: false,
            //         scales: {
            //             y: {
            //                 beginAtZero: true,
            //                 ticks: {
            //                     callback: function (value) {
            //                         return value.toLocaleString() + ' UGX';
            //                     }
            //                 }
            //             }
            //         },
            //         plugins: {
            //             tooltip: {
            //                 callbacks: {
            //                     label: function (context) {
            //                         return context.raw.toLocaleString() + ' UGX';
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            // new Chart(categoryCtx, {
            //     type: 'doughnut',
            //     data: {
            //         labels: {!! json_encode($categoryData['labels']) !!},
            //         datasets: [{
            //             data: {!! json_encode($categoryData['values']) !!},
            //             backgroundColor: [
            //                 '#5e72e4', '#2dce89', '#fb6340', '#11cdef', '#f5365c',
            //                 '#ffd600', '#8965e0', '#f3a4b5', '#adb5bd', '#172b4d'
            //             ],
            //             borderWidth: 0
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         maintainAspectRatio: false,
            //         plugins: {
            //             legend: {
            //                 position: 'bottom',
            //                 labels: {
            //                     boxWidth: 12
            //                 }
            //             },
            //             tooltip: {
            //                 callbacks: {
            //                     label: function (context) {
            //                         return context.label + ': ' +
            //                             context.raw.toLocaleString() + ' UGX (' +
            //                             Math.round(context.raw / context.dataset.data.reduce((a, b) => a + b) * 100) + '%)';
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // });
        });
    </script>
@endpush