@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1>Inventory Analytics</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retailer.analytics.index') }}">Analytics</a></li>
            <li class="breadcrumb-item active">Inventory</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-box-seam display-4" style="color: #d98323;"></i>
                    </div>
                    <h4>{{ number_format($inventoryData['stock_levels']['in_stock'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">Items In Stock</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                    </div>
                    <h4>{{ number_format($inventoryData['stock_levels']['low_stock'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">Low Stock Items</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-x-circle display-4 text-danger"></i>
                    </div>
                    <h4>{{ number_format($inventoryData['stock_levels']['out_of_stock'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">Out of Stock</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-currency-exchange display-4" style="color: #d98323;"></i>
                    </div>
                    <h4>UGX {{ number_format($inventoryData['total_inventory_value'] ?? 0, 0) }}</h4>
                    <p class="text-muted mb-0">Total Inventory Value</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Value Items -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Highest Value Inventory Items</h5>
                    @if(isset($inventoryData['top_value_items']) && $inventoryData['top_value_items']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryData['top_value_items']->take(15) as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['product_name'] }}</strong>
                                    </td>
                                    <td>{{ number_format($item['quantity']) }}</td>
                                    <td>UGX {{ number_format($item['unit_price'], 0) }}</td>
                                    <td>UGX {{ number_format($item['total_value'], 0) }}</td>
                                    <td>
                                        @if($item['status'] === 'low')
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No inventory data available.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Inventory Status Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stock Status Distribution</h5>
                    <div id="stockStatusChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Turnover Analysis -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Inventory Turnover Analysis <span>| Last 30 Days</span></h5>
                    @if(isset($inventoryData['turnover_data']) && $inventoryData['turnover_data']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Sold (30 Days)</th>
                                    <th>Turnover Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryData['turnover_data'] as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['product_name'] }}</strong>
                                    </td>
                                    <td>{{ number_format($item['current_stock']) }}</td>
                                    <td>{{ number_format($item['sold_quantity']) }}</td>
                                    <td>{{ number_format($item['turnover_rate'], 2) }}</td>
                                    <td>
                                        @if($item['turnover_rate'] > 1)
                                            <span class="badge bg-success">High Turnover</span>
                                        @elseif($item['turnover_rate'] > 0.5)
                                            <span class="badge bg-warning">Medium Turnover</span>
                                        @else
                                            <span class="badge bg-danger">Low Turnover</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No turnover data available for analysis.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts -->
    @if((isset($inventoryData['stock_levels']['low_stock']) && $inventoryData['stock_levels']['low_stock'] > 0) || 
        (isset($inventoryData['stock_levels']['out_of_stock']) && $inventoryData['stock_levels']['out_of_stock'] > 0))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Stock Alerts
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($inventoryData['stock_levels']['out_of_stock']) && $inventoryData['stock_levels']['out_of_stock'] > 0)
                    <div class="alert alert-danger">
                        <strong>{{ $inventoryData['stock_levels']['out_of_stock'] }} items are completely out of stock!</strong>
                        <br><small>These items need immediate restocking to avoid lost sales.</small>
                    </div>
                    @endif
                    
                    @if(isset($inventoryData['stock_levels']['low_stock']) && $inventoryData['stock_levels']['low_stock'] > 0)
                    <div class="alert alert-warning">
                        <strong>{{ $inventoryData['stock_levels']['low_stock'] }} items are running low on stock.</strong>
                        <br><small>Consider reordering these items soon to maintain adequate inventory levels.</small>
                    </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('inventories.index') }}" class="btn btn-primary me-2">
                            <i class="bi bi-box-seam"></i> Manage Inventory
                        </a>
                        <a href="{{ route('inventories.reorders') }}" class="btn btn-warning me-2">
                            <i class="bi bi-arrow-repeat"></i> View Reorders
                        </a>
                        <a href="{{ route('retailer.reports.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i> Generate Inventory Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('inventories.adjustments') }}" class="btn btn-primary">
                            <i class="bi bi-gear"></i> Inventory Adjustments
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Add New Product
                        </a>
                        <a href="{{ route('retailer.reports.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i> Generate Report
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
document.addEventListener("DOMContentLoaded", () => {
    // Stock Status Chart
    const stockLevels = @json($inventoryData['stock_levels'] ?? []);
    const stockLabels = ['In Stock', 'Low Stock', 'Out of Stock'];
    const stockData = [
        stockLevels.in_stock || 0,
        stockLevels.low_stock || 0,
        stockLevels.out_of_stock || 0
    ];

    new ApexCharts(document.querySelector("#stockStatusChart"), {
        series: stockData,
        chart: {
            height: 350,
            type: 'donut',
        },
        labels: stockLabels,
        colors: ['#28a745', '#ffc107', '#dc3545'],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 'bold'
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            fontWeight: 'bold',
                            color: '#d98323'
                        },
                        total: {
                            show: true,
                            label: 'Total Items',
                            fontSize: '14px',
                            color: '#666',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex]
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center'
        },
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
