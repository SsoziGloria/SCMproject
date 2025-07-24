@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1>Retailer Reports</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Generate Business Report</h5>
                    
                    <form action="{{ route('retailer.reports.generate') }}" method="POST" target="_blank">
                        @csrf
                        
                        <div class="row mb-3">
                            <label for="report_type" class="col-sm-3 col-form-label">Report Type</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="report_type" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    <option value="sales">Sales Report</option>
                                    <option value="inventory">Inventory Report</option>
                                    <option value="customer-summary">Customer Summary</option>
                                    <option value="comprehensive">Comprehensive Report</option>
                                </select>
                                <div class="form-text">Choose the type of report you want to generate</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date_from" class="col-sm-3 col-form-label">From Date</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date_to" class="col-sm-3 col-form-label">To Date</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-download"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Report Types</h5>
                    
                    <div class="report-type-info">
                        <div class="mb-3">
                            <h6><i class="bi bi-graph-up" style="color: #d98323;"></i> Sales Report</h6>
                            <p class="text-muted small">Revenue analytics, order trends, top products, and daily sales performance.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-box-seam" style="color: #d98323;"></i> Inventory Report</h6>
                            <p class="text-muted small">Stock levels, inventory value, low stock alerts, and inventory movements.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-people" style="color: #d98323;"></i> Customer Summary</h6>
                            <p class="text-muted small">Customer analytics, spending patterns, and customer segmentation insights.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-file-earmark-text" style="color: #d98323;"></i> Comprehensive Report</h6>
                            <p class="text-muted small">Complete business overview combining sales, inventory, and customer data.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Stats</h5>
                    
                    <div class="quick-stats">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>This Month Revenue</span>
                            <span class="fw-bold" style="color: #d98323;">UGX {{ number_format(\App\Models\Order::whereBetween('created_at', [now()->startOfMonth(), now()])->sum('total_amount'), 0) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Total Orders</span>
                            <span class="fw-bold">{{ number_format(\App\Models\Order::whereBetween('created_at', [now()->startOfMonth(), now()])->count()) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Active Customers</span>
                            <span class="fw-bold">{{ number_format(\App\Models\User::whereHas('orders', function($q) { $q->whereBetween('created_at', [now()->startOfMonth(), now()]); })->count()) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Products In Stock</span>
                            <span class="fw-bold">{{ number_format(\App\Models\Inventory::where('quantity', '>', 0)->count()) }}</span>
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
document.addEventListener("DOMContentLoaded", function() {
    // Update date validation
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    dateFrom.addEventListener('change', function() {
        dateTo.min = this.value;
    });
    
    dateTo.addEventListener('change', function() {
        dateFrom.max = this.value;
    });
});
</script>
@endpush
