@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1>Analytics Overview</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Analytics</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <!-- Key Metrics Cards -->
        <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Revenue <span>| Last 30 Days</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h6>UGX {{ number_format($metrics['revenue']['current'], 0) }}</h6>
                            <span class="text-{{ $metrics['revenue']['change'] >= 0 ? 'success' : 'danger' }} small pt-1 fw-bold">
                                {{ $metrics['revenue']['change'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue']['change'], 1) }}%
                            </span>
                            <span class="text-muted small pt-2 ps-1">vs previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Orders <span>| Last 30 Days</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ number_format($metrics['orders']['current']) }}</h6>
                            <span class="text-{{ $metrics['orders']['change'] >= 0 ? 'success' : 'danger' }} small pt-1 fw-bold">
                                {{ $metrics['orders']['change'] >= 0 ? '+' : '' }}{{ number_format($metrics['orders']['change'], 1) }}%
                            </span>
                            <span class="text-muted small pt-2 ps-1">vs previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <h5 class="card-title">Avg Order Value</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="ps-3">
                            <h6>UGX {{ number_format($metrics['avg_order_value']['current'], 0) }}</h6>
                            <span class="text-{{ $metrics['avg_order_value']['change'] >= 0 ? 'success' : 'danger' }} small pt-1 fw-bold">
                                {{ $metrics['avg_order_value']['change'] >= 0 ? '+' : '' }}{{ number_format($metrics['avg_order_value']['change'], 1) }}%
                            </span>
                            <span class="text-muted small pt-2 ps-1">vs previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card inventory-card">
                <div class="card-body">
                    <h5 class="card-title">Active Customers</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: #d98323; color: white; width: 64px; height: 64px;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{ number_format($metrics['customers']) }}</h6>
                            @if($metrics['low_stock_items'] > 0)
                                <span class="text-warning small pt-1 fw-bold">
                                    {{ $metrics['low_stock_items'] }} low stock items
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
        <!-- Charts Section -->
        <div class="col-lg-8">
            <!-- Sales Chart -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daily Sales Trend <span>| Last 30 Days</span></h5>
                    <div id="dailySalesChart"></div>
                </div>
            </div>
            
            <!-- Category Performance Chart -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Sales by Category</h5>
                    <div id="categorySalesChart"></div>
                </div>
            </div>
        </div>

        <!-- Analytics Navigation -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Analytics Modules</h5>
                    
                    <div class="analytics-nav">
                        <a href="{{ route('retailer.analytics.sales') }}" class="btn btn-outline-primary btn-lg w-100 mb-3">
                            <i class="bi bi-graph-up"></i> Sales Analytics
                        </a>
                        
                        <a href="{{ route('retailer.analytics.inventory') }}" class="btn btn-outline-primary btn-lg w-100 mb-3">
                            <i class="bi bi-box-seam"></i> Inventory Analytics
                        </a>
                        
                        <a href="{{ route('retailer.analytics.customers') }}" class="btn btn-outline-primary btn-lg w-100 mb-3">
                            <i class="bi bi-people"></i> Customer Analytics
                        </a>
                        
                        <a href="{{ route('retailer.reports.index') }}" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-file-earmark-text"></i> Generate Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Insights -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Insights</h5>
                    
                    <div class="insight-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Best Sales Day</span>
                            <span style="color: #d98323;">
                                @if(isset($chartData['daily_sales']) && $chartData['daily_sales']->count() > 0)
                                    {{ $chartData['daily_sales']->sortByDesc('revenue')->first()->date ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="insight-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Top Category</span>
                            <span style="color: #d98323;">
                                @if(isset($chartData['category_sales']) && $chartData['category_sales']->count() > 0)
                                    {{ $chartData['category_sales']->first()->name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="insight-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Growth Trend</span>
                            <span class="text-{{ $metrics['revenue']['change'] >= 0 ? 'success' : 'danger' }}">
                                {{ $metrics['revenue']['change'] >= 0 ? '↗ Growing' : '↘ Declining' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Daily Sales Chart
    const dailySalesData = @json($chartData['daily_sales'] ?? []);
    const salesLabels = dailySalesData.map(item => new Date(item.date).toLocaleDateString());
    const salesRevenue = dailySalesData.map(item => item.revenue);
    const salesOrders = dailySalesData.map(item => item.order_count);

    new ApexCharts(document.querySelector("#dailySalesChart"), {
        series: [{
            name: 'Revenue (UGX)',
            type: 'area',
            data: salesRevenue
        }, {
            name: 'Orders',
            type: 'line',
            data: salesOrders
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            }
        },
        stroke: {
            width: [0, 4],
            curve: 'smooth'
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                colorStops: [{
                    offset: 0,
                    color: '#d98323',
                    opacity: 0.3
                }, {
                    offset: 100,
                    color: '#d98323',
                    opacity: 0.1
                }]
            }
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
        }],
        legend: {
            horizontalAlign: 'left'
        }
    }).render();

    // Category Sales Chart
    const categorySalesData = @json($chartData['category_sales'] ?? []);
    const categoryLabels = categorySalesData.map(item => item.name);
    const categoryRevenue = categorySalesData.map(item => item.total_revenue);

    new ApexCharts(document.querySelector("#categorySalesChart"), {
        series: [{
            name: 'Revenue',
            data: categoryRevenue
        }],
        chart: {
            height: 350,
            type: 'bar',
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: categoryLabels,
        },
        colors: ['#d98323'],
        grid: {
            borderColor: '#f1f1f1',
        }
    }).render();
});
</script>
@endpush
