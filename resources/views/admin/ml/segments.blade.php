@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">Customer Segments</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Customer Segments</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
            <button class="btn btn-primary ms-2" onclick="runMLModels()">
                <i class="bi bi-arrow-clockwise"></i> Refresh Data
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cluster Statistics Cards -->
    <div class="row mb-4">
        @foreach($clusterStats as $stat)
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">Cluster {{ $stat->cluster }}</h6>
                            <h3 class="fw-bold mb-2">{{ $stat->count }}</h3>
                            <small class="text-muted">Customers</small>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-people-fill text-muted" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <hr>
                    <div class="text-start">
                        <small class="text-muted">Avg Quantity: <strong>{{ number_format($stat->avg_quantity, 1) }}</strong></small><br>
                        <small class="text-muted">Total Volume: <strong>{{ number_format($stat->total_quantity) }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Segments Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Customer Segment Details</h5>
            <span class="badge bg-light text-dark">{{ $segments->total() }} total segments</span>
        </div>
        <div class="card-body p-0">
            @if($segments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Customer ID</th>
                                <th>Cluster</th>
                                <th>Average Quantity</th>
                                <th>Total Quantity</th>
                                <th>Purchase Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($segments as $segment)
                            <tr>
                                <td class="ps-3">
                                    <strong>Customer {{ $segment->customer_id }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        Cluster {{ $segment->cluster }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($segment->quantity) }}</span> units
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($segment->total_quantity) }}</span> units
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $segment->purchase_count }}</span> purchases
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewCustomerDetails({{ $segment->customer_id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="applyToMarketing({{ $segment->customer_id }}, {{ $segment->cluster }})">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-center">
                        {{ $segments->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-people" style="font-size: 2rem; color: #ddd;"></i>
                        <p class="mt-2 mb-0 text-muted">No customer segments found</p>
                        <button class="btn btn-primary mt-3" onclick="runMLModels()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Run Analysis
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function runMLModels() {
    if (confirm('Re-run ML analysis to refresh customer segments?')) {
        const runBtn = event.target;
        const originalText = runBtn.innerHTML;
        
        runBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Running Analysis...';
        runBtn.disabled = true;
        
        fetch('{{ route("admin.run-ml-analysis") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'ML Analysis completed successfully! Refreshing page...');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('danger', 'ML Analysis failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'ML Analysis failed: Network error occurred');
        })
        .finally(() => {
            runBtn.innerHTML = originalText;
            runBtn.disabled = false;
        });
    }
}

function viewCustomerDetails(customerId) {
    // Implementation for viewing customer details
    alert('View details for Customer ' + customerId);
}

function applyToMarketing(customerId, cluster) {
    // Implementation for applying to marketing
    alert('Apply marketing strategy for Customer ' + customerId + ' in Cluster ' + cluster);
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
}
</script>
@endpush

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush
