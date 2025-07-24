@extends('admin.app')

@section('content')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-success fw-bold">Revenue Analytics</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('analytics') }}">Analytics</a></li>
                    <li class="breadcrumb-item active">Revenue</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Analytics
            </a>
            <a href="{{ route('analytics.products') }}" class="btn btn-outline-success">
                <i class="bi bi-box-seam"></i> Product Analytics
            </a>
            <a href="{{ route('analytics.users') }}" class="btn btn-outline-info">
                <i class="bi bi-people"></i> User Analytics
            </a>
        </div>
    </div><!-- End Page Title -->

    <section class="section revenue-analytics">

        <!-- Revenue Summary Cards -->
        <div class="row mb-4">
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Revenue</h6>
                                <h3 class="fw-bold text-success mb-2">UGX {{ number_format($analytics['total_revenue']) }}</h3>
                                <small class="text-muted">All time revenue</small>
                            </div>
                            <div class="card-icon bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-currency-dollar text-success fs-2"></i>
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
                                <h6 class="card-title text-muted mb-1">Monthly Revenue</h6>
                                <h3 class="fw-bold text-primary mb-2">UGX {{ number_format($analytics['monthly_revenue']) }}</h3>
                                <small class="text-muted">This month</small>
                            </div>
                            <div class="card-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-calendar-month text-primary fs-2"></i>
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
                                <h6 class="card-title text-muted mb-1">Daily Revenue</h6>
                                <h3 class="fw-bold text-info mb-2">UGX {{ number_format($analytics['daily_revenue']) }}</h3>
                                <small class="text-muted">Today</small>
                            </div>
                            <div class="card-icon bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-calendar-day text-info fs-2"></i>
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
                                <h6 class="card-title text-muted mb-1">Revenue Growth</h6>
                                <h3 class="fw-bold text-{{ $analytics['revenue_growth'] >= 0 ? 'success' : 'danger' }} mb-2">
                                    {{ $analytics['revenue_growth'] > 0 ? '+' : '' }}{{ $analytics['revenue_growth'] }}%
                                </h3>
                                <small class="text-muted">vs last month</small>
                            </div>
                            <div class="card-icon bg-{{ $analytics['revenue_growth'] >= 0 ? 'success' : 'danger' }} bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-{{ $analytics['revenue_growth'] >= 0 ? 'trending-up' : 'trending-down' }} text-{{ $analytics['revenue_growth'] >= 0 ? 'success' : 'danger' }} fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Charts -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="monthlyRevenueChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Revenue by Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="paymentMethodChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown Tables -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Revenue by Product</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th>Quantity Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['revenue_by_product'] as $product)
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-primary">{{ $product->product_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($product->total_revenue) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No product revenue data</p>
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
                        <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Revenue by Category</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Category</th>
                                        <th>Revenue</th>
                                        <th>Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalCategoryRevenue = $analytics['revenue_by_category']->sum('total_revenue'); @endphp
                                    @forelse($analytics['revenue_by_category'] as $category)
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-primary">{{ $category->category ?? 'Uncategorized' }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($category->total_revenue) }}</span>
                                            </td>
                                            <td>
                                                @php $percentage = $totalCategoryRevenue > 0 ? ($category->total_revenue / $totalCategoryRevenue) * 100 : 0; @endphp
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
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
                                                <p class="text-muted mt-2">No category revenue data</p>
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

        <!-- Payment Methods and Order Status -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Revenue by Payment Method</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Payment Method</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['revenue_by_payment_method'] as $payment)
                                        <tr>
                                            <td class="ps-3">
                                                <i class="bi bi-{{ $payment->payment_method === 'cash' ? 'cash' : ($payment->payment_method === 'card' ? 'credit-card' : 'phone') }} me-2 text-muted"></i>
                                                <strong>{{ ucfirst($payment->payment_method ?? 'Unknown') }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $payment->order_count }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($payment->total_revenue) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-credit-card text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No payment method data</p>
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
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Revenue by Order Status</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Status</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['revenue_by_status'] as $status)
                                        <tr>
                                            <td class="ps-3">
                                                <span class="badge bg-{{ 
                                                    $status->status === 'pending' ? 'warning' :
                                                    ($status->status === 'processing' ? 'info' :
                                                        ($status->status === 'shipped' ? 'primary' :
                                                            ($status->status === 'delivered' ? 'success' : 'danger')))
                                                }}">
                                                    {{ ucfirst($status->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $status->order_count }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($status->total_revenue) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-list-check text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No status revenue data</p>
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
        // Monthly Revenue Chart
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
                labels: @json($analytics['monthly_revenue_chart']['labels']),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: @json($analytics['monthly_revenue_chart']['data']),
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
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
                                return 'UGX ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Payment Method Chart
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: @json($analytics['revenue_by_payment_method']->pluck('payment_method')->map(function($method) { return ucfirst($method ?? 'Unknown'); })),
                datasets: [{
                    data: @json($analytics['revenue_by_payment_method']->pluck('total_revenue')),
                    backgroundColor: [
                        '#28a745',
                        '#007bff', 
                        '#ffc107',
                        '#dc3545',
                        '#6c757d'
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
