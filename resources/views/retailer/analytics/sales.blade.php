@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1>Sales Analytics</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retailer.analytics.index') }}">Analytics</a></li>
            <li class="breadcrumb-item active">Sales</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <!-- Period Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('retailer.analytics.sales') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="period" class="form-label">Time Period</label>
                            <select class="form-select" id="period" name="period" onchange="toggleCustomDates()">
                                <option value="7_days" {{ $selectedPeriod == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30_days" {{ $selectedPeriod == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90_days" {{ $selectedPeriod == '90_days' ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="custom" {{ $selectedPeriod == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="start_date_container" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }};">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3" id="end_date_container" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }};">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Selling Products</h5>
                    @if(isset($salesData['top_products']) && $salesData['top_products']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Product Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Revenue</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesData['top_products'] as $index => $product)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: #d98323;">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>
                                    <td>UGX {{ number_format($product->price, 0) }}</td>
                                    <td>{{ number_format($product->total_quantity) }}</td>
                                    <td>UGX {{ number_format($product->total_revenue, 0) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="background-color: #d98323; width: {{ ($product->total_revenue / $salesData['top_products']->first()->total_revenue) * 100 }}%;">
                                                {{ number_format(($product->total_revenue / $salesData['top_products']->first()->total_revenue) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No sales data available for the selected period.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Status Analysis -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Orders by Status</h5>
                    @if(isset($salesData['orders_by_status']) && $salesData['orders_by_status']->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Revenue</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalOrders = $salesData['orders_by_status']->sum('count'); @endphp
                                @foreach($salesData['orders_by_status'] as $status)
                                <tr>
                                    <td>
                                        <span class="badge 
                                            @if($status->status == 'completed') bg-success
                                            @elseif($status->status == 'pending') bg-warning
                                            @elseif($status->status == 'shipped') bg-info
                                            @elseif($status->status == 'cancelled') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($status->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($status->count) }}</td>
                                    <td>UGX {{ number_format($status->revenue, 0) }}</td>
                                    <td>{{ number_format(($status->count / $totalOrders) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No order status data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hourly Sales Pattern -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales by Hour of Day</h5>
                    @if(isset($salesData['hourly_sales']) && $salesData['hourly_sales']->count() > 0)
                    <div id="hourlySalesChart"></div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No hourly sales data available for analysis.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('retailer.reports.index') }}" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text"></i> Generate Sales Report
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> View All Orders
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-seam"></i> Manage Products
                        </a>
                        <a href="{{ route('retailer.analytics.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Analytics Overview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const startContainer = document.getElementById('start_date_container');
    const endContainer = document.getElementById('end_date_container');
    
    if (period === 'custom') {
        startContainer.style.display = 'block';
        endContainer.style.display = 'block';
    } else {
        startContainer.style.display = 'none';
        endContainer.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", () => {
    @if(isset($salesData['hourly_sales']) && $salesData['hourly_sales']->count() > 0)
    // Hourly Sales Chart
    const hourlySalesData = @json($salesData['hourly_sales']);
    const hourLabels = hourlySalesData.map(item => `${item.hour}:00`);
    const hourlyOrders = hourlySalesData.map(item => item.order_count);
    const hourlyRevenue = hourlySalesData.map(item => item.avg_revenue);

    new ApexCharts(document.querySelector("#hourlySalesChart"), {
        series: [{
            name: 'Orders',
            type: 'column',
            data: hourlyOrders
        }, {
            name: 'Avg Revenue',
            type: 'line',
            data: hourlyRevenue
        }],
        chart: {
            height: 350,
            type: 'line'
        },
        stroke: {
            width: [0, 4]
        },
        title: {
            text: 'Orders and Revenue by Hour'
        },
        labels: hourLabels,
        colors: ['#d98323', '#28a745'],
        xaxis: {
            type: 'category',
            title: {
                text: 'Hour of Day'
            }
        },
        yaxis: [{
            title: {
                text: 'Number of Orders',
            }
        }, {
            opposite: true,
            title: {
                text: 'Average Revenue (UGX)'
            }
        }]
    }).render();
    @endif
});
</script>
@endpush
