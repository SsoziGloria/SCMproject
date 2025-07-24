@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12">
            <h1 class="h3 mb-0">Reports Center</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="d-flex justify-content-lg-end justify-content-start mt-3 mt-lg-0">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                    <i class="bi bi-file-earmark-plus me-1"></i> Generate New Report
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Report Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-primary w-100 p-3" onclick="quickReport('sales')">
                                <i class="bi bi-graph-up fs-4 d-block mb-2"></i>
                                <span>Sales Report</span>
                                <small class="d-block text-muted">Last 30 days</small>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-success w-100 p-3" onclick="quickReport('inventory')">
                                <i class="bi bi-boxes fs-4 d-block mb-2"></i>
                                <span>Inventory Report</span>
                                <small class="d-block text-muted">Current stock</small>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-info w-100 p-3" onclick="quickReport('ml-analysis')">
                                <i class="bi bi-robot fs-4 d-block mb-2"></i>
                                <span>ML Analysis</span>
                                <small class="d-block text-muted">Latest insights</small>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-warning w-100 p-3" onclick="quickReport('customer-segments')">
                                <i class="bi bi-people fs-4 d-block mb-2"></i>
                                <span>Customer Segments</span>
                                <small class="d-block text-muted">Segmentation data</small>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-secondary w-100 p-3" onclick="quickReport('demand-forecast')">
                                <i class="bi bi-graph-up-arrow fs-4 d-block mb-2"></i>
                                <span>Demand Forecast</span>
                                <small class="d-block text-muted">Predictions</small>
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <button class="btn btn-outline-danger w-100 p-3" onclick="quickReport('comprehensive')">
                                <i class="bi bi-file-earmark-text fs-4 d-block mb-2"></i>
                                <span>Comprehensive</span>
                                <small class="d-block text-muted">Full business report</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Reports</h5>
                    <span class="badge bg-primary">{{ $recentReports->count() }} reports</span>
                </div>
                <div class="card-body p-0">
                    @if($recentReports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Report Name</th>
                                        <th>Type</th>
                                        <th>Period</th>
                                        <th>Format</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReports as $report)
                                        <tr>
                                            <td>
                                                <strong>{{ $report->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($report->type) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $report->date_from->format('M d') }} - {{ $report->date_to->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ strtoupper($report->format) }}</span>
                                            </td>
                                            <td>
                                                @if($report->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($report->status === 'processing')
                                                    <span class="badge bg-warning">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($report->status === 'completed' && $report->file_path)
                                                    <a href="#" onclick="downloadReport({{ $report->id }})" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-2">No Reports Generated Yet</h6>
                            <p class="text-muted">Click "Generate New Report" to create your first report</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateReportForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" name="type" required>
                                <option value="">Select report type...</option>
                                @foreach($reportTypes as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Format</label>
                            <select class="form-select" name="format" required>
                                <option value="pdf">PDF Document</option>
                                <option value="csv">CSV Spreadsheet</option>
                                <option value="excel">Excel Workbook (.xlsx)</option>
                                <option value="json">JSON Data</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Recipients (Optional)</label>
                        <input type="email" class="form-control" name="email_recipients" placeholder="email1@example.com, email2@example.com">
                        <small class="form-text text-muted">Separate multiple emails with commas</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Schedule Frequency (Optional)</label>
                        <select class="form-select" name="schedule_frequency">
                            <option value="">One-time report</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="generateSpinner"></span>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Set default date range to last 30 days
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.querySelector('input[name="date_to"]').value = today.toISOString().split('T')[0];
    document.querySelector('input[name="date_from"]').value = thirtyDaysAgo.toISOString().split('T')[0];
});

// Quick report generation
function quickReport(type) {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    generateReport({
        type: type,
        format: 'pdf',
        date_from: thirtyDaysAgo.toISOString().split('T')[0],
        date_to: today.toISOString().split('T')[0]
    });
}

// Generate report form submission
document.getElementById('generateReportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    generateReport(data);
});

// Generate report function
function generateReport(data) {
    const spinner = document.getElementById('generateSpinner');
    const submitBtn = document.querySelector('#generateReportForm button[type="submit"]');
    
    if (spinner) spinner.classList.remove('d-none');
    if (submitBtn) submitBtn.disabled = true;
    
    showAlert('info', 'Generating report... This may take a few moments.');
    
    fetch('{{ route('admin.reports.generate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            if (data.download_url) {
                setTimeout(() => {
                    window.location.href = data.download_url;
                }, 1000);
            }
            // Close modal and refresh page
            const modal = bootstrap.Modal.getInstance(document.getElementById('generateReportModal'));
            if (modal) modal.hide();
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(data.message || 'Report generation failed');
        }
    })
    .catch(error => {
        console.error('Report generation error:', error);
        showAlert('danger', 'Report generation failed: ' + error.message);
    })
    .finally(() => {
        if (spinner) spinner.classList.add('d-none');
        if (submitBtn) submitBtn.disabled = false;
    });
}

// Download report
function downloadReport(reportId) {
    window.location.href = `/admin/reports/download/${reportId}`;
}

// Show alert function (reuse from admin dashboard)
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const contentDiv = document.querySelector('.container-fluid');
    if (contentDiv) {
        contentDiv.insertAdjacentHTML('afterbegin', alertHtml);
    }
}
</script>
@endpush
