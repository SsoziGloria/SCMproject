@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12">
            <h1 class="h3 mb-0">Admin Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end justify-content-start mt-3 mt-lg-0">
                @if(empty($segments) || $segments->isEmpty())
                    <button id="runMLBtn" class="btn btn-primary" onclick="runMLModels()">
                        <i class="bi bi-play-circle me-1"></i> Run ML Analysis
                    </button>
                @else
                    <button id="refreshMLBtn" class="btn btn-outline-primary" onclick="runMLModels()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh ML Data
                    </button>
                @endif
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info">
                    <i class="bi bi-file-earmark-text me-1"></i> Reports Center
                </a>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list-task me-1"></i> Manage Tasks
                </a>
                <a href="{{ route('workforce.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-people me-1"></i> Workforce
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Enhanced Metrics Cards Row -->
    <div class="row mb-4">
        <!-- Pending Orders Card -->
        <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
            <div class="card shadow-sm h-100 hover-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Orders</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($pendingOrders ?? 0) }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>Awaiting processing
                            </small>
                        </div>
                        <div class="card-icon bg-warning bg-opacity-10 rounded">
                            <i class="bi bi-hourglass-split text-warning fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returns Card -->
        <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
            <div class="card shadow-sm h-100 hover-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Returns</h6>
                            <h3 class="mb-0 text-danger">{{ number_format($returns ?? 0) }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-arrow-return-left me-1"></i>Cancelled orders
                            </small>
                        </div>
                        <div class="card-icon bg-danger bg-opacity-10 rounded">
                            <i class="bi bi-arrow-counterclockwise text-danger fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivered Orders Card -->
        <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
            <div class="card shadow-sm h-100 hover-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Delivered</h6>
                            <h3 class="mb-0 text-success">{{ number_format($deliveredOrders ?? 0) }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-check-circle me-1"></i>Successfully completed
                            </small>
                        </div>
                        <div class="card-icon bg-success bg-opacity-10 rounded">
                            <i class="bi bi-truck text-success fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
            <div class="card shadow-sm h-100 hover-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="mb-0 text-primary">UGX {{ number_format($totalRevenue ?? 0, 0) }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-cash-stack me-1"></i>From delivered orders
                            </small>
                        </div>
                        <div class="card-icon bg-primary bg-opacity-10 rounded">
                            <i class="bi bi-graph-up text-primary fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>








        <!-- Quick Actions Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-list-ul fs-4 d-block mb-2"></i>
                                    <span>All Orders</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-success w-100 p-3">
                                    <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
                                    <span>Products</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('inventories.index') }}" class="btn btn-outline-warning w-100 p-3">
                                    <i class="bi bi-archive fs-4 d-block mb-2"></i>
                                    <span>Inventory</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-info w-100 p-3">
                                    <i class="bi bi-tags fs-4 d-block mb-2"></i>
                                    <span>Categories</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary w-100 p-3">
                                    <i class="bi bi-truck fs-4 d-block mb-2"></i>
                                    <span>Shipments</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('analytics') }}" class="btn btn-outline-danger w-100 p-3">
                                    <i class="bi bi-graph-up fs-4 d-block mb-2"></i>
                                    <span>Analytics</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Second Row for Additional Features -->
                        <div class="row g-3 mt-2">
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary w-100 p-3">
                                    <i class="bi bi-shop fs-4 d-block mb-2"></i>
                                    <span>Shop</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('admin.vendor-validation') }}" class="btn btn-outline-success w-100 p-3">
                                    <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
                                    <span>Vendor Validation</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('workers.index') }}" class="btn btn-outline-warning w-100 p-3">
                                    <i class="bi bi-people-fill fs-4 d-block mb-2"></i>
                                    <span>Workers</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('workforce.index') }}" class="btn btn-outline-info w-100 p-3">
                                    <i class="bi bi-person-workspace fs-4 d-block mb-2"></i>
                                    <span>Workforce</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('productReviews.index') }}" class="btn btn-outline-secondary w-100 p-3">
                                    <i class="bi bi-star fs-4 d-block mb-2"></i>
                                    <span>Reviews</span>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-danger w-100 p-3">
                                    <i class="bi bi-gear fs-4 d-block mb-2"></i>
                                    <span>Settings</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(empty($segments) || $segments->isEmpty())
            <!-- ML Setup Required Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-warning bg-warning bg-opacity-10">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-robot text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-warning fw-bold mb-3">Machine Learning Analysis Required</h4>
                            <p class="text-muted mb-4">
                                Customer segmentation and demand prediction models haven't been run yet. 
                                Click below to generate ML insights for your supply chain.
                            </p>
                                                        <button type="button" class="btn btn-gradient-primary btn-lg w-100" onclick="runMLModels()">
                                <i class="bi bi-cpu-fill me-2"></i>
                                Run ML Analysis
                            </button>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    This process may take a few minutes to complete
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Separate ML Charts Section -->
            <div class="row mb-4">
                <!-- Customer Segments Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-people-fill me-2"></i>Customer Segments Analysis
                            </h6>
                            <a href="{{ route('admin.segments') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-eye"></i> View All
                            </a>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                                <canvas id="segmentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Demand Predictions Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-graph-up-arrow me-2"></i>Demand Forecast Analysis
                            </h6>
                            <a href="{{ route('admin.predictions') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-eye"></i> View All
                            </a>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                                <canvas id="predictionsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-arrow-down-up me-2"></i>ML Data Integration</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <button class="btn btn-outline-primary w-100 p-3" onclick="applySegmentTargeting()">
                                        <i class="bi bi-bullseye fs-4 d-block mb-2"></i>
                                        <span>Apply Customer Targeting</span>
                                        <small class="d-block text-muted">Use segments for marketing</small>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button class="btn btn-outline-success w-100 p-3" onclick="adjustInventoryLevels()">
                                        <i class="bi bi-boxes fs-4 d-block mb-2"></i>
                                        <span>Auto-Adjust Inventory</span>
                                        <small class="d-block text-muted">Based on demand predictions</small>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button class="btn btn-outline-warning w-100 p-3" onclick="generateRecommendations()">
                                        <i class="bi bi-lightbulb fs-4 d-block mb-2"></i>
                                        <span>Product Recommendations</span>
                                        <small class="d-block text-muted">AI-powered suggestions</small>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button class="btn btn-outline-info w-100 p-3" onclick="exportMLData()">
                                        <i class="bi bi-download fs-4 d-block mb-2"></i>
                                        <span>Export ML Data</span>
                                        <small class="d-block text-muted">Download insights as CSV</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- ML Data Tables Row -->
            <div class="row mb-4">
                <!-- Customer Segments Table -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="bi bi-people-fill me-2"></i>Customer Segments</h6>
                            <span class="badge bg-light text-dark">{{ $segments->count() }} customers</span>
                        </div>
                        <div class="card-body p-0">
                            @if($segments->count() > 0)
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="ps-3">Customer</th>
                                                <th>Quantity</th>
                                                <th>Total Qty</th>
                                                <th>Purchases</th>
                                                <th>Cluster</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($segments as $seg)
                                                <tr>
                                                    <td class="ps-3">
                                                        <strong class="text-primary">Customer {{ $seg->customer_id }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">{{ number_format($seg->quantity, 1) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ number_format($seg->total_quantity, 1) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $seg->purchase_count }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning text-dark">Cluster {{ $seg->cluster }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($segments->count() >= 50)
                                    <div class="card-footer text-center">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Showing first 50 segments. <a href="#" onclick="alert('Full segments view coming soon!')">View all</a>
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted mt-2">No Customer Segments</h6>
                                    <p class="text-muted small">Run ML Analysis to generate customer segments</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Demand Predictions Table -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Demand Predictions</h6>
                            <span class="badge bg-light text-dark">{{ $predictions->count() }} predictions</span>
                        </div>
                        <div class="card-body p-0">
                            @if($predictions->count() > 0)
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="ps-3">Product</th>
                                                <th>Date</th>
                                                <th>Predicted Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($predictions as $pred)
                                                <tr>
                                                    <td class="ps-3">
                                                        <strong class="text-success">{{ $pred->product_id }}</strong>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $pred->prediction_date }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ number_format($pred->predicted_quantity) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($predictions->count() >= 20)
                                    <div class="card-footer text-center">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Showing recent 20 predictions. <a href="#" onclick="alert('Full predictions view coming soon!')">View all</a>
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted mt-2">No Demand Predictions</h6>
                                    <p class="text-muted small">Run ML Analysis to generate demand forecasts</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cluster Analysis Section -->
            @if(isset($clusterSummaries) && $clusterSummaries->isNotEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Customer Cluster Analysis</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Cluster</th>
                                            <th>Customers</th>
                                            <th>Profile</th>
                                            <th>Product Types</th>
                                            <th>Strategy</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clusterSummaries as $summary)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary fs-6">Cluster {{ $summary->cluster }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-info">{{ $summary->customer_count }}</span>
                                                </td>
                                                <td>{{ Str::limit($summary->description, 50) }}</td>
                                                <td>{{ Str::limit($summary->product_types, 40) }}</td>
                                                <td>{{ Str::limit($summary->recommendation_strategy, 40) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
</div>
@endsection

@push('styles')
<style>
    .card-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hover-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.08);
    }
    
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
        border-color: rgba(0,0,0,0.15);
    }

    .chart-container canvas {
        max-height: 400px !important;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spin {
        animation: spin 1s linear infinite;
    }
    
    .loading-spinner {
        display: none;
    }
    
    .loading-spinner.show {
        display: inline-block;
    }

    /* Compact metrics cards */
    .card-body h3 {
        font-weight: 700;
        font-size: 1.75rem;
    }

    .card-body h6 {
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body small {
        font-size: 0.75rem;
    }

    /* Enhanced alert positioning */
    .alert {
        margin-bottom: 1rem;
        border: none;
        border-left: 4px solid;
    }

    .alert-info { border-left-color: #0dcaf0; }
    .alert-success { border-left-color: #198754; }
    .alert-danger { border-left-color: #dc3545; }
    .alert-warning { border-left-color: #ffc107; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    @if(!empty($segments) && $segments->isNotEmpty())
        // Initialize Chart only if data exists
        initializeMLChart();
    @endif
});

@if(!empty($segments) && $segments->isNotEmpty())
function initializeMLChart() {
    // Customer Segments Chart
    const segmentLabels = @json($segments->pluck('customer_id')->map(fn($id) => 'Customer ' . $id));
    const segmentData = @json($segments->pluck('total_quantity'));
    const segmentClusters = @json($segments->pluck('cluster'));

    const segmentCtx = document.getElementById('segmentsChart').getContext('2d');
    const segmentsChart = new Chart(segmentCtx, {
        type: 'bar',
        data: {
            labels: segmentLabels.slice(0, 15), // Show first 15 for readability
            datasets: [{
                label: 'Total Quantity (Units)',
                data: segmentData.slice(0, 15),
                backgroundColor: segmentClusters.slice(0, 15).map(cluster => {
                    const colors = {
                        0: 'rgba(255, 99, 132, 0.8)',
                        1: 'rgba(54, 162, 235, 0.8)', 
                        2: 'rgba(255, 206, 86, 0.8)',
                        3: 'rgba(75, 192, 192, 0.8)',
                        4: 'rgba(153, 102, 255, 0.8)'
                    };
                    return colors[cluster] || 'rgba(201, 203, 207, 0.8)';
                }),
                borderColor: segmentClusters.slice(0, 15).map(cluster => {
                    const colors = {
                        0: 'rgba(255, 99, 132, 1)',
                        1: 'rgba(54, 162, 235, 1)', 
                        2: 'rgba(255, 206, 86, 1)',
                        3: 'rgba(75, 192, 192, 1)',
                        4: 'rgba(153, 102, 255, 1)'
                    };
                    return colors[cluster] || 'rgba(201, 203, 207, 1)';
                }),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            return 'Cluster: ' + segmentClusters[index];
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity (Units)'
                    }
                }
            }
        }
    });

    // Demand Predictions Chart
    const predictionLabels = @json($predictions->pluck('prediction_date'));
    const predictionData = @json($predictions->pluck('predicted_quantity'));
    const predictionProducts = @json($predictions->pluck('product_id'));

    const predictionCtx = document.getElementById('predictionsChart').getContext('2d');
    const predictionsChart = new Chart(predictionCtx, {
        type: 'line',
        data: {
            labels: predictionLabels.slice(0, 20), // Show first 20 predictions
            datasets: [{
                label: 'Predicted Demand',
                data: predictionData.slice(0, 20),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            return 'Product: ' + predictionProducts[index];
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Prediction Date'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Predicted Quantity'
                    }
                }
            }
        }
    });
}
@endif

// ML Functions - Always available regardless of data state
function runMLModels() {
    console.log('🚀 ML Analysis function called');
    
    // Get the button that was clicked
    const runBtn = document.getElementById('runMLBtn') || document.getElementById('refreshMLBtn');
    if (!runBtn) {
        console.error('❌ ML button not found');
        showAlert('danger', 'Button not found. Please refresh the page.');
        return;
    }
    
    console.log('✅ Button found:', runBtn.id);
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        showAlert('danger', 'Security token not found. Please refresh the page.');
        return;
    }
    
    console.log('✅ CSRF token found:', csrfToken.getAttribute('content').substring(0, 10) + '...');
    
    const originalText = runBtn.innerHTML;
    
    // Show loading state
    runBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Running ML Analysis...';
    runBtn.disabled = true;
    
    // Show initial alert
    showAlert('info', 'ML Analysis started. This may take a few minutes...');
    console.log('🔄 Starting ML Analysis...');
    
    // Make AJAX request to run ML models
    fetch('/admin/run-ml-analysis', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log('📡 Response received:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok,
            headers: Object.fromEntries(response.headers.entries())
        });
        
        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('Session expired. Please refresh the page and try again.');
            }
            if (response.status === 500) {
                return response.text().then(text => {
                    console.error('💥 Server error response:', text);
                    throw new Error(`Server Error (${response.status}): Check console for details`);
                });
            }
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ ML Analysis response:', data);
        
        if (data.success) {
            // Show success message with details
            const message = data.message || 'ML Analysis completed successfully!';
            const details = data.data ? 
                ` Created ${data.data.segments_created || 0} segments and ${data.data.predictions_created || 0} predictions.` : '';
            
            showAlert('success', message + details + ' Refreshing page...');
            
            // Refresh page after 3 seconds to show the message
            setTimeout(() => {
                console.log('🔄 Refreshing page...');
                window.location.reload();
            }, 3000);
        } else {
            throw new Error(data.message || 'ML Analysis failed');
        }
    })
    .catch(error => {
        console.error('💥 ML Analysis Error:', error);
        
        // Show detailed error message
        let errorMessage = 'ML Analysis failed: ' + error.message;
        showAlert('danger', errorMessage);
        
        // Reset button
        runBtn.innerHTML = originalText;
        runBtn.disabled = false;
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert alert at the top of the container
    const contentDiv = document.querySelector('.container-fluid');
    if (contentDiv) {
        contentDiv.insertAdjacentHTML('afterbegin', alertHtml);
    } else {
        // Fallback to body if container not found
        document.body.insertAdjacentHTML('afterbegin', alertHtml);
    }
}

// ML Integration Functions
function applySegmentTargeting() {
    if (!confirm('Apply customer segment data to create targeted marketing campaigns?')) return;
    
    showAlert('info', 'Applying customer segment targeting...');
    
    fetch('/admin/ml/apply-segmentation', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Customer targeting applied successfully!');
        } else {
            throw new Error(data.message || 'Failed to apply segmentation');
        }
    })
    .catch(error => {
        showAlert('danger', 'Error: ' + error.message);
    });
}

function adjustInventoryLevels() {
    if (!confirm('Automatically adjust inventory levels based on demand predictions?')) return;
    
    showAlert('info', 'Adjusting inventory levels based on ML predictions...');
    
    fetch('/admin/ml/adjust-inventory', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Inventory levels adjusted successfully!');
        } else {
            throw new Error(data.message || 'Failed to adjust inventory');
        }
    })
    .catch(error => {
        showAlert('danger', 'Error: ' + error.message);
    });
}

function generateRecommendations() {
    showAlert('info', 'Generating AI-powered product recommendations...');
    
    fetch('/admin/ml/generate-recommendations', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Recommendations generated successfully!');
            if (data.recommendations) {
                // Display recommendations in a modal or alert
                const recommendations = data.recommendations.map(r => `• ${r}`).join('\n');
                alert('AI Recommendations:\n\n' + recommendations);
            }
        } else {
            throw new Error(data.message || 'Failed to generate recommendations');
        }
    })
    .catch(error => {
        showAlert('danger', 'Error: ' + error.message);
    });
}

function exportMLData() {
    showAlert('info', 'Preparing ML data export...');
    
    // Create download link
    const link = document.createElement('a');
    link.href = '{{ route('admin.ml.export-data') }}';
    link.download = 'ml-analysis-' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showAlert('success', 'ML data export started!');
}
</script>
@endpush