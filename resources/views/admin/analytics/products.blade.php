@extends('admin.app')

@section('content')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-success fw-bold">Product Analytics</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('analytics') }}">Analytics</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Analytics
            </a>
            <a href="{{ route('analytics.revenue') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> Revenue Analytics
            </a>
            <a href="{{ route('analytics.users') }}" class="btn btn-outline-info">
                <i class="bi bi-people"></i> User Analytics
            </a>
        </div>
    </div><!-- End Page Title -->

    <section class="section product-analytics">

        <!-- Product Summary Cards -->
        <div class="row mb-4">
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Products</h6>
                                <h3 class="fw-bold text-primary mb-2">{{ number_format($analytics['total_products']) }}</h3>
                                <small class="text-muted">All products</small>
                            </div>
                            <div class="card-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-box-seam text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Active Products</h6>
                                <h3 class="fw-bold text-success mb-2">{{ number_format($analytics['active_products']) }}</h3>
                                <small class="text-muted">Available for sale</small>
                            </div>
                            <div class="card-icon bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-check-circle text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Out of Stock</h6>
                                <h3 class="fw-bold text-danger mb-2">{{ number_format($analytics['out_of_stock']) }}</h3>
                                <small class="text-muted">Need restocking</small>
                            </div>
                            <div class="card-icon bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Low Stock</h6>
                                <h3 class="fw-bold text-warning mb-2">{{ number_format($analytics['low_stock']) }}</h3>
                                <small class="text-muted">Below reorder level</small>
                            </div>
                            <div class="card-icon bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-exclamation-circle text-warning fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Inventory Value: UGX {{ number_format($analytics['inventory_value']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Charts -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Top Selling Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="productSalesChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Category Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="categoryChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Levels Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Current Stock Levels</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="stockLevelsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Performance Tables -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Selling Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['top_selling_products'] as $product)
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
                                                <p class="text-muted mt-2">No sales data available</p>
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
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-down me-2"></i>Least Selling Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['least_selling_products'] as $product)
                                        <tr>
                                            <td class="ps-3">
                                                <div>
                                                    <strong class="text-primary">{{ $product->product_name }}</strong>
                                                    <br><small class="text-muted">ID: {{ $product->product_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ number_format($product->total_quantity) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">UGX {{ number_format($product->total_revenue ?? 0) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No sales data available</p>
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

        <!-- Product Categories -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Product Categories</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Category</th>
                                        <th>Product Count</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalProducts = $analytics['product_categories']->sum('count'); @endphp
                                    @forelse($analytics['product_categories'] as $category)
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-primary">{{ $category->category ?? 'Uncategorized' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $category->count }}</span>
                                            </td>
                                            <td>
                                                @php $percentage = $totalProducts > 0 ? ($category->count / $totalProducts) * 100 : 0; @endphp
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: {{ $percentage }}%" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-tags text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No categories found</p>
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
        // Product Sales Chart
        const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
        new Chart(productSalesCtx, {
            type: 'bar',
            data: {
                labels: @json($analytics['product_sales_chart']['labels']),
                datasets: [{
                    label: 'Quantity Sold',
                    data: @json($analytics['product_sales_chart']['data']),
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgb(40, 167, 69)',
                    borderWidth: 1
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
                        title: {
                            display: true,
                            text: 'Quantity Sold'
                        }
                    }
                }
            }
        });

        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($analytics['category_distribution_chart']['labels']),
                datasets: [{
                    data: @json($analytics['category_distribution_chart']['data']),
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#6c757d',
                        '#17a2b8',
                        '#fd7e14'
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

        // Stock Levels Chart
        const stockLevelsCtx = document.getElementById('stockLevelsChart').getContext('2d');
        new Chart(stockLevelsCtx, {
            type: 'bar',
            data: {
                labels: @json($analytics['stock_levels_chart']['labels']),
                datasets: [{
                    label: 'Stock Level',
                    data: @json($analytics['stock_levels_chart']['data']),
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgb(0, 123, 255)',
                    borderWidth: 1
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
                        title: {
                            display: true,
                            text: 'Stock Quantity'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Products'
                        }
                    }
                }
            }
        });
    </script>
@endpush
