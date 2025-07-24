@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">Demand Predictions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Demand Predictions</li>
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

    <!-- Product Statistics Cards -->
    <div class="row mb-4">;
        @foreach($productStats as $stat)
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">{{ $stat->product_id }}</h6>
                            <h3 class="fw-bold mb-2">{{ number_format($stat->total_predicted) }}</h3>
                            <small class="text-muted">Total Predicted</small>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-graph-up-arrow text-muted" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <hr>
                    <div class="text-start">
                        <small class="text-muted">Predictions: <strong>{{ $stat->prediction_count }}</strong></small><br>
                        <small class="text-muted">Avg Demand: <strong>{{ number_format($stat->avg_prediction, 1) }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Predictions Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Demand Prediction Details</h5>
            <span class="badge bg-light text-dark">{{ $predictions->total() }} total predictions</span>
        </div>
        <div class="card-body p-0">
            @if($predictions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Prediction Date</th>
                                <th>Predicted Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($predictions as $prediction)
                            <tr>
                                <td class="ps-3">
                                    <strong>{{ $prediction->product_id }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($prediction->prediction_date)->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($prediction->predicted_quantity) }}</span> units
                                </td>
                                <td>
                                    @php
                                        $date = \Carbon\Carbon::parse($prediction->prediction_date);
                                        $daysFromNow = $date->diffInDays(now(), false);
                                        if ($daysFromNow <= 7) {
                                            echo '<span class="badge bg-secondary">Next 7 days</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Day ' . ceil($daysFromNow) . '</span>';
                                        }
                                    @endphp
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewPredictionDetails('{{ $prediction->product_id }}', '{{ $prediction->prediction_date }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="applyToInventory('{{ $prediction->product_id }}', {{ $prediction->predicted_quantity }})">
                                            <i class="bi bi-boxes"></i>
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
                        {{ $predictions->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-graph-up" style="font-size: 2rem; color: #ddd;"></i>
                        <p class="mt-2 mb-0 text-muted">No demand predictions found</p>
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
    if (confirm('Re-run ML analysis to refresh demand predictions?')) {
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

function viewPredictionDetails(productId, predictionDate) {
    // Implementation for viewing prediction details
    alert('View details for ' + productId + ' on ' + predictionDate);
}

function applyToInventory(productId, quantity) {
    // Implementation for applying to inventory
    alert('Apply ' + quantity + ' units of ' + productId + ' to inventory');
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