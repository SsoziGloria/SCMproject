@extends('retailer.app')

@section('content')
@php
    $metrics = $metrics ?? [
        'current_revenue' => 0,
        'revenue_change' => 0,
        'current_orders' => 0,
        'order_change' => 0,
        'total_customers' => 0,
        'new_customers' => 0,
        'inventory_value' => 0,
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
    ];
    $top_products = $top_products ?? collect();
    $recent_orders = $recent_orders ?? collect();
    $low_stock_items = $low_stock_items ?? collect();
    $out_of_stock_items = $out_of_stock_items ?? collect();
    $sales_chart_data = $sales_chart_data ?? collect();
    $order_status_data = $order_status_data ?? collect();
    $category_performance = $category_performance ?? collect();
    $recentActivity = $recentActivity ?? collect();
    $pendingOrders = $pendingOrders ?? 0;
    $returns = $returns ?? 0;
    $inventoryCount = $inventoryCount ?? 0;
    $deliveredOrders = $deliveredOrders ?? 0;
@endphp

<div class="pagetitle">
    <h1>Retailer Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <!-- Sales Metrics Row -->
        <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Revenue <span>| This Month</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h6>UGX {{ number_format($metrics['current_revenue'], 0) }}</h6>
                            <span class="text-{{ $metrics['revenue_change'] >= 0 ? 'success' : 'danger' }} small pt-1 fw-bold">
                                {{ $metrics['revenue_change'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue_change'], 1) }}%
                            </span>
                            <span class="text-muted small pt-2 ps-1">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Orders <span>| This Month</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ number_format($metrics['current_orders']) }}</h6>
                            <span class="text-{{ $metrics['order_change'] >= 0 ? 'success' : 'danger' }} small pt-1 fw-bold">
                                {{ $metrics['order_change'] >= 0 ? '+' : '' }}{{ number_format($metrics['order_change'], 1) }}%
                            </span>
                            <span class="text-muted small pt-2 ps-1">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <h5 class="card-title">Customers <span>| This Month</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ number_format($metrics['total_customers']) }}</h6>
                            <span class="text-info small pt-1 fw-bold">
                                {{ number_format($metrics['new_customers']) }}
                            </span>
                            <span class="text-muted small pt-2 ps-1">new customers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card inventory-card">
                <div class="card-body">
                    <h5 class="card-title">Inventory Value</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="ps-3">
                            <h6>UGX {{ number_format($metrics['inventory_value'], 0) }}</h6>
                            @if($metrics['low_stock_count'] > 0)
                                <span class="text-warning small pt-1 fw-bold">
                                    {{ $metrics['low_stock_count'] }} low stock
                                </span>
                            @else
                                <span class="text-success small pt-1 fw-bold">All items in stock</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
            <!-- Sales Chart -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Overview <span>| Last 7 Days</span></h5>
                    <div id="salesChart"></div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card top-selling mt-4">
                <div class="card-body pb-0">
                    <h5 class="card-title">Top Products <span>| This Month</span></h5>
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Sold</th>
                                <th scope="col">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_products as $product)
                            <tr>
                                <td><a href="#" class="text-primary fw-bold">{{ $product->name }}</a></td>
                                <td>UGX {{ number_format($product->price, 0) }}</td>
                                <td class="fw-bold">{{ number_format($product->total_quantity) }}</td>
                                <td>UGX {{ number_format($product->total_revenue, 0) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No sales data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right side columns -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Orders</h5>
                    <div class="activity">
                        @forelse($recent_orders->take(5) as $order)
                        <div class="activity-item d-flex">
                            <div class="activite-label">{{ $order->created_at->diffForHumans() }}</div>
                            <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                            <div class="activity-content">
                                <strong>{{ $order->user->name ?? 'Customer' }}</strong> placed order 
                                <a href="{{ route('orders.show', $order->id) }}" class="fw-bold text-dark">#{{ $order->id }}</a>
                                <br><small class="text-muted">UGX {{ number_format($order->total_amount, 0) }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No recent orders</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Stock Alerts -->
            @if($low_stock_items->count() > 0 || $out_of_stock_items->count() > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stock Alerts</h5>
                    
                    @if($out_of_stock_items->count() > 0)
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle"></i> Out of Stock ({{ $out_of_stock_items->count() }})</h6>
                        @foreach($out_of_stock_items->take(3) as $item)
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $item->product->name }}</span>
                            <span class="badge bg-danger">0</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($low_stock_items->count() > 0)
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-circle"></i> Low Stock ({{ $low_stock_items->count() }})</h6>
                        @foreach($low_stock_items->take(3) as $item)
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $item->product->name }}</span>
                            <span class="badge bg-warning">{{ $item->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <a href="{{ route('inventories.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-seam"></i> Manage Inventory
                    </a>
                </div>
            </div>
            @endif

            <!-- Order Status Distribution -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Status Distribution</h5>
                    <div id="orderStatusChart"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Sales Chart
    const salesChartData = @json($sales_chart_data);
    const salesLabels = salesChartData.map(item => new Date(item.date).toLocaleDateString());
    const salesRevenue = salesChartData.map(item => item.revenue);
    const salesOrders = salesChartData.map(item => item.order_count);

    new ApexCharts(document.querySelector("#salesChart"), {
        series: [{
            name: 'Revenue (UGX)',
            type: 'column',
            data: salesRevenue
        }, {
            name: 'Orders',
            type: 'line',
            data: salesOrders
        }],
        chart: {
            height: 350,
            type: 'line',
        },
        stroke: {
            width: [0, 4]
        },
        title: {
            text: 'Daily Sales Performance'
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
        },
        labels: salesLabels,
        colors: ['#d98323', '#28a745'],
        xaxis: {
            type: 'category'
        },
        yaxis: [{
            title: {
                text: 'Revenue (UGX)',
            }
        }, {
            opposite: true,
            title: {
                text: 'Orders'
            }
        }]
    }).render();

    // Order Status Chart
    const orderStatusData = @json($order_status_data);
    const statusLabels = orderStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
    const statusCounts = orderStatusData.map(item => item.count);

    new ApexCharts(document.querySelector("#orderStatusChart"), {
        series: statusCounts,
        chart: {
            height: 350,
            type: 'donut',
        },
        labels: statusLabels,
        colors: ['#d98323', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }).render();
});
</script>
@endpush

<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
            <div class="row">
                <!-- Pending Orders Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card border-info shadow-sm">
                        <div class="card-body text-start">
                            <h5 class="card-title">Pending Orders</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-info text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="fw-bold">{{ $pendingOrders ?? 0 }}</h6>
                                    <a href="{{ route('orders.pending') }}"
                                        class="btn btn-sm btn-outline-info mt-2">View</a>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6>{{ $inventoryCount ?? 0 }}</h6>
                                <span class="text-muted small pt-2 ps-1">items in stock</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End PendingOrders Card -->

                <!-- Returns Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card returns-card">
                        <div class="card-body">
                            <h5 class="card-title">Returns</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-warning text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="fw-bold">{{ $returns ?? 0 }}</h6>
                                    <a href="{{ route('orders.cancelled') }}"
                                        class="btn btn-sm btn-outline-warning mt-2">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Returns Card -->

                <!-- Low Stock Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card low-stock-card">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $low_stock_items->count() ?? 0 }}</h6>
                                    <span class="text-muted small pt-2 ps-1">items low</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Low Stock Card -->

                <!-- Delivered Orders Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card supplierMessage-card">
                        <div class="card-body">
                            <h5 class="card-title">Delivered Orders</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-success text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="fw-bold">{{ $deliveredOrders ?? 0 }}</h6>
                                    <a href="{{ route('orders.completed') }}"
                                        class="btn btn-sm btn-outline-success mt-2">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Delivered Orders Card -->
            </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
            <!-- Recent Activity (e.g., recent inventory changes) -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Inventory Activity</h5>
                    <div class="activity">
                        @foreach($recentActivity as $activity)
                        <div class="activity-item d-flex">
                            <div class="activite-label">
                                {{ $activity->time_ago }}
                            </div>
                            <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                            <div class="activity-content">
                                {{ $activity->description }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div><!-- End Recent Activity -->

            <!-- News & Updates (optional) -->
            <div class="card">
                <div class="card-body pb-0">
                    <h5 class="card-title">News &amp; Updates</h5>
                    <div class="news">
                        <!-- Supplier-related news here -->
                    </div>
                </div>
            </div><!-- End News & Updates -->
        </div><!-- End Right side columns -->
    </div>
</section>
@endsection