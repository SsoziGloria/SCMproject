@extends('admin.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Title -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Vendor Document Validation</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vendor Validation</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.vendor-validation.history') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history"></i> View History
                </a>
            </div>
        </div>

        <!-- Service Health Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Validation Service Status</h6>
                <button class="btn btn-sm btn-outline-primary" onclick="checkHealth()">
                    <i class="bi bi-arrow-clockwise"></i> Check Service Health
                </button>
            </div>
            <div class="card-body">
                <div id="health-status">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Checking service status...
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending vendors list -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pending Verifications</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($pendingVendors as $pendingVendor)
                        <a href="{{ route('admin.vendor-validation', ['vendor_id' => $pendingVendor->vendor_id]) }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $pendingVendor->company_name }}</h6>
                                <p class="text-muted small mb-0">{{ $pendingVendor->name }} · {{ $pendingVendor->email }}</p>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </a>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">No pending verifications</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Vendor validation form -->
        <div class="col-md-7">
            @if($vendor)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Validate: {{ $vendor->company_name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <br>
                            <h4>Vendor Information</h4>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Company Name</th>
                                    <td>{{ $vendor->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Person</th>
                                    <td>{{ $vendor->contact_person }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $vendor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $vendor->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $vendor->address }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $vendor->country }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="mb-4">
                            <h6>Document</h6>
                            <div class="d-flex align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-pdf" style="font-size: 2rem;"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <p class="mb-1">{{ basename($vendor->pdf_path) }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('admin.vendor-validation.download', $vendor->vendor_id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Validation interface -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6>API Validation</h6>

                                <!-- Validation form for Java API integration -->
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" id="validateBtn">
                                        <i class="bi bi-check2-circle"></i> Validate Document
                                    </button>
                                </div>

                                <!-- Validation results will be shown here -->
                                <div id="validation-results" class="mt-3 d-none">
                                    <div class="alert alert-info">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Processing document...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manual validation form -->
                        <form action="{{ route('admin.vendor-validation.update-status', $vendor->vendor_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <h6>Manual Validation</h6>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="d-flex">
                                    <div class="form-check me-4">
                                        <input class="form-check-input" type="radio" name="status" id="status-approved"
                                            value="Approved" required>
                                        <label class="form-check-label" for="status-approved">
                                            Approve
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status-rejected"
                                            value="Rejected" required>
                                        <label class="form-check-label" for="status-rejected">
                                            Reject
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message (Optional)</label>
                                <textarea class="form-control" id="message" name="message" rows="2"></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-arrow-left-circle" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Select a vendor from the list</h5>
                        <p class="text-muted">Choose a pending vendor to validate their documents</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">

            <!-- Manual Validation Toggle Button -->
            <div class="col-12 mb-3">
                <button type="button" class="btn btn-outline-info" id="manualValidationBtn">
                    <i class="bi bi-pencil-square"></i> Manual Validation
                </button>
            </div>

            <!-- Manual Validation Cards (initially hidden) -->
            <div id="manualValidationCards" class="row d-none">
                <!-- Validation Form Card -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Upload Document</h6>
                        </div>
                        <div class="card-body">
                            <form id="validation-form" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="vendor_id" class="form-label">Select Vendor</label>
                                    <select class="form-select" id="vendor_id" name="vendor_id" required>
                                        <option value="">Select a vendor...</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->name }} ({{ $vendor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload PDF Document</label>
                                    <input class="form-control" type="file" id="file" name="file" accept=".pdf" required>
                                    <div class="form-text">Only PDF files are accepted. Maximum size: 10MB</div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" id='submit-button' class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Validate Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Results Card -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold">Validation Results</h6>
                        </div>
                        <div class="card-body">
                            <div id="result">
                                <p class="text-center text-muted py-5">
                                    <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                                    <br><br>
                                    Submit a document to see validation results
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const manualBtn = document.getElementById('manualValidationBtn');
                    const manualCards = document.getElementById('manualValidationCards');
                    manualBtn.addEventListener('click', function () {
                        manualCards.classList.toggle('d-none');
                    });
                });
            </script>
        </div>

        <!-- Validation History Card -->
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Recent Validation History</h6>
            </div>
            <div class="card-body">
                <div id="validation-history">
                    <p class="text-center text-muted py-5">
                        Select a vendor to view validation history
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Details Modal -->
    <div class="modal fade" id="validationDetailsModal" tabindex="-1" aria-labelledby="validationDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationDetailsModalLabel">Validation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="validation-details-content">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="download-document-btn">Download Document</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const vendorSelect = document.getElementById('vendor_id');
        const fileInput = document.getElementById('file');
        const submitButton = document.getElementById('submit-button');

        function checkFormValidity() {
            const vendorSelected = vendorSelect.value !== '';
            const fileSelected = fileInput.files.length > 0;

            // The button is enabled only if both a vendor and a file are selected
            submitButton.disabled = !(vendorSelected && fileSelected);
        }

        // Add event listeners to check validity whenever the user makes a change
        vendorSelect.addEventListener('change', checkFormValidity);
        fileInput.addEventListener('change', checkFormValidity);

        // Run once on page load to set the initial state
        document.addEventListener('DOMContentLoaded', function () {
            checkHealth();
            checkFormValidity(); // Disable button initially
        });


        // Handle form submission
        document.getElementById('validation-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Manually create the FormData object for robustness
            const formData = new FormData();
            formData.append('vendor_id', document.getElementById('vendor_id').value);
            formData.append('file', document.getElementById('file').files[0]);

            const resultDiv = document.getElementById('result');

            // Show loading state
            resultDiv.innerHTML = `
                                                                                                                                                                                                                            <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                                                                                                                                                                                                                <div class="spinner-border text-primary mb-3" role="status">
                                                                                                                                                                                                                                    <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <p class="text-muted">Validating document, please wait...</p>
                                                                                                                                                                                                                            </div>`;

            try {
                const response = await axios.post('/api/vendor-validation/validate', formData);


                displayResult(response.data);
                loadValidationHistory(formData.get('vendor_id'));

            } catch (error) {
                if (error.response && error.response.data) {
                    displayResult(error.response.data, false);
                } else {
                    displayResult({
                        success: false,
                        message: 'Network error occurred. Please try again later.'
                    }, false);
                }
            }
        });

        // Display validation result
        function displayResult(data) {
            const resultDiv = document.getElementById('result');

            // Check the 'valid' key for the business logic result, not the 'success' key.
            const isValid = data.valid === true;

            let html = `
                                                                                                                                <div class="text-center mb-4">
                                                                                                                                    <div class="badge bg-${isValid ? 'success' : 'danger'} p-3 mb-3">
                                                                                                                                        <i class="bi bi-${isValid ? 'check-circle' : 'x-circle'}" style="font-size: 2rem;"></i>
                                                                                                                                    </div>
                                                                                                                                    <h4>${isValid ? 'Document is Valid' : 'Document is Invalid'}</h4>
                                                                                                                                    <p class="text-muted">${data.message}</p>
                                                                                                                                </div>`;

            // Note: The JSON key from Java is 'validationResults' (camelCase).
            // Your old code used 'validation_details', which might be why it was null.
            if (data.validationResults) {
                html += '<div class="table-responsive mt-3">';
                html += '<table class="table table-bordered">';
                html += '<thead class="table-light"><tr><th>Check</th><th>Status</th></tr></thead>';
                html += '<tbody>';

                for (const [key, value] of Object.entries(data.validationResults)) {
                    const status = value ? 'success' : 'danger';
                    const icon = value ? 'check-circle' : 'x-circle';
                    html += `
                                                                                                                                        <tr>
                                                                                                                                            <td>${formatCheckName(key)}</td>
                                                                                                                                            <td><span class="text-${status}"><i class="bi bi-${icon}"></i> ${value ? 'Pass' : 'Fail'}</span></td>
                                                                                                                                        </tr>`;
                }

                html += '</tbody></table></div>';
            }

            resultDiv.innerHTML = html;
        }


        // Format check names for display
        function formatCheckName(key) {
            const names = {
                'hasCompanyName': 'Company Name',
                'hasAddress': 'Address Information',
                'hasContactInfo': 'Contact Information',
                'hasCertification': 'Certification Details',
                'hasFinancialInfo': 'Financial Information',
                'hasComplianceInfo': 'Compliance Information',
                'hasValidFormat': 'Valid Format'
            };
            return names[key] || key;
        }

        // Check validation service health
        async function checkHealth() {
            const statusDiv = document.getElementById('health-status');
            statusDiv.innerHTML = `
                                                                                                                                                                                                                            <div class="d-flex align-items-center">
                                                                                                                                                                                                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                                                                                                                                                                                                                    <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                Checking service status...
                                                                                                                                                                                                                            </div>`;

            try {
                const response = await axios.get('/api/service-health/vendor-validation');
                const status = response.data.status;

                if (status === 'UP') {
                    statusDiv.innerHTML = `
                                                                                                                                                                                                                                    <div class="alert alert-success d-flex align-items-center m-0">
                                                                                                                                                                                                                                        <i class="bi bi-check-circle-fill me-2"></i>
                                                                                                                                                                                                                                        <div>Validation service is online and ready</div>
                                                                                                                                                                                                                                    </div>`;
                } else {
                    statusDiv.innerHTML = `
                                                                                                                                                                                                                                    <div class="alert alert-warning d-flex align-items-center m-0">
                                                                                                                                                                                                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                                                                                                                                                                                        <div>Validation service is available but reported issues</div>
                                                                                                                                                                                                                                    </div>`;
                }
            } catch (error) {
                console.error("Health check failed:", error);
                statusDiv.innerHTML = `
                                                                                                                                                                                                                                <div class="alert alert-danger d-flex align-items-center m-0">
                                                                                                                                                                                                                                    <i class="bi bi-x-circle-fill me-2"></i>
                                                                                                                                                                                                                                    <div>Cannot connect to validation service. Please check if the service is running.</div>
                                                                                                                                                                                                                                </div>`;
            }
        }

        // Load validation history for selected vendor
        async function loadValidationHistory(vendorId) {
            if (!vendorId) {
                document.getElementById('validation-history').innerHTML = `
                                                                                                                                                                                                                                <p class="text-center text-muted py-5">
                                                                                                                                                                                                                                    Select a vendor to view validation history
                                                                                                                                                                                                                                </p>`;
                return;
            }

            const historyDiv = document.getElementById('validation-history');
            historyDiv.innerHTML = `
                                                                                                                                                                                                                            <div class="d-flex justify-content-center py-5">
                                                                                                                                                                                                                                <div class="spinner-border text-primary" role="status">
                                                                                                                                                                                                                                    <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>`;

            try {
                const response = await axios.get(`/api/vendor-validation/vendor/${vendorId}/history`);
                const validations = response.data.validations;

                if (validations.length === 0) {
                    historyDiv.innerHTML = `
                                                                                                                                                                                                                                    <div class="text-center py-5">
                                                                                                                                                                                                                                        <i class="bi bi-inbox" style="font-size: 2.5rem; color: #ccc;"></i>
                                                                                                                                                                                                                                        <p class="mt-3 text-muted">No validation history found for this vendor</p>
                                                                                                                                                                                                                                    </div>`;
                    return;
                }

                let html = '<div class="table-responsive">';
                html += '<table class="table table-hover align-middle">';
                html += `
                                                                                                                                                                                                                                <thead class="table-light">
                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                        <th>Date</th>
                                                                                                                                                                                                                                        <th>Document</th>
                                                                                                                                                                                                                                        <th>Status</th>
                                                                                                                                                                                                                                        <th>Actions</th>
                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                </thead>`;
                html += '<tbody>';

                validations.forEach(validation => {
                    const date = new Date(validation.created_at).toLocaleString();
                    const statusClass = validation.is_valid ? 'success' : 'danger';
                    const statusIcon = validation.is_valid ? 'check-circle' : 'x-circle';
                    const statusText = validation.is_valid ? 'Valid' : 'Invalid';

                    html += `
                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                        <td>${date}</td>
                                                                                                                                                                                                                                        <td class="text-truncate" style="max-width: 200px;">${validation.original_filename}</td>
                                                                                                                                                                                                                                        <td><span class="badge bg-${statusClass}"><i class="bi bi-${statusIcon}"></i> ${statusText}</span></td>
                                                                                                                                                                                                                                        <td>
                                                                                                                                                                                                                                            <div class="btn-group">
                                                                                                                                                                                                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewValidation(${validation.id})">
                                                                                                                                                                                                                                                    <i class="bi bi-eye"></i>
                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="revalidate(${validation.id})">
                                                                                                                                                                                                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                        </td>
                                                                                                                                                                                                                                    </tr>`;
                });

                html += '</tbody></table></div>';
                historyDiv.innerHTML = html;

            } catch (error) {
                historyDiv.innerHTML = `
                                                                                                                                                                                                                                <div class="alert alert-danger m-0">
                                                                                                                                                                                                                                    <i class="bi bi-exclamation-triangle-fill"></i> 
                                                                                                                                                                                                                                    Error loading validation history
                                                                                                                                                                                                                                </div>`;
            }
        }

        // View validation details
        async function viewValidation(validationId) {
            const modalBody = document.getElementById('validation-details-content');
            const modal = new bootstrap.Modal(document.getElementById('validationDetailsModal'));

            modalBody.innerHTML = `
                                                                                                                                                                                                                            <div class="d-flex justify-content-center py-4">
                                                                                                                                                                                                                                <div class="spinner-border text-primary" role="status">
                                                                                                                                                                                                                                    <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>`;

            modal.show();

            try {
                const response = await axios.get(`/api/vendor-validation/validation/${validationId}`);
                const validation = response.data.validation;

                // Set download button href
                const downloadBtn = document.getElementById('download-document-btn');
                downloadBtn.onclick = () => window.location.href = `/admin/vendor-validation/download/${validation.id}`;

                const date = new Date(validation.created_at).toLocaleString();
                const statusClass = validation.is_valid ? 'success' : 'danger';
                const statusIcon = validation.is_valid ? 'check-circle' : 'x-circle';

                let html = `
                                                                                                                                                                                                                                <div class="mb-4 p-3 bg-light rounded">
                                                                                                                                                                                                                                    <div class="d-flex justify-content-between">
                                                                                                                                                                                                                                        <div>
                                                                                                                                                                                                                                            <h6 class="mb-1">Document</h6>
                                                                                                                                                                                                                                            <p class="mb-0">${validation.original_filename}</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <span class="badge bg-${statusClass} d-flex align-items-center">
                                                                                                                                                                                                                                            <i class="bi bi-${statusIcon} me-1"></i> 
                                                                                                                                                                                                                                            ${validation.is_valid ? 'Valid' : 'Invalid'}
                                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                <div class="mb-3">
                                                                                                                                                                                                                                    <h6>Validation Message</h6>
                                                                                                                                                                                                                                    <p>${validation.validation_message || 'No message provided'}</p>
                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                <div class="mb-3">
                                                                                                                                                                                                                                    <h6>Date</h6>
                                                                                                                                                                                                                                    <p>${date}</p>
                                                                                                                                                                                                                                </div>`;

                if (validation.validation_details) {
                    html += '<div class="mb-3"><h6>Validation Checks</h6>';
                    html += '<ul class="list-group">';

                    for (const [key, value] of Object.entries(validation.validation_details)) {
                        const itemClass = value ? 'success' : 'danger';
                        const icon = value ? 'check-circle' : 'x-circle';

                        html += `
                                                                                                                                                                                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                                                                                                                                                            ${formatCheckName(key)}
                                                                                                                                                                                                                                            <span class="badge bg-${itemClass}"><i class="bi bi-${icon}"></i></span>
                                                                                                                                                                                                                                        </li>`;
                    }

                    html += '</ul></div>';
                }

                modalBody.innerHTML = html;

            } catch (error) {
                modalBody.innerHTML = `
                                                                                                                                                                                                                                <div class="alert alert-danger">
                                                                                                                                                                                                                                    <i class="bi bi-exclamation-triangle-fill"></i> 
                                                                                                                                                                                                                                    Error loading validation details
                                                                                                                                                                                                                                </div>`;
            }
        }

        // Revalidate document
        async function revalidate(validationId) {
            if (!confirm('Are you sure you want to revalidate this document?')) return;

            const vendorId = document.getElementById('vendor_id').value;

            try {
                const response = await axios.post(`/api/vendor-validation/validation/${validationId}/revalidate`);

                // Use the new validation result to update the main display card.
                displayResult(response.data);

                // Show a success toast
                const toast = new bootstrap.Toast(document.getElementById('toast'));
                document.getElementById('toast-body').textContent = 'Document successfully revalidated';
                toast.show();

                // Refresh the history table
                if (vendorId) {
                    loadValidationHistory(vendorId);
                }

            } catch (error) {
                // Log the detailed error object to the browser console
                console.error("Revalidation failed:", error.response || error);
                alert('Error revalidating document. Check the console (F12) for details.');
            }

        }

        // Load history when vendor changes
        document.getElementById('vendor_id').addEventListener('change', function () {
            loadValidationHistory(this.value);
        });

        // Check health on page load
        document.addEventListener('DOMContentLoaded', function () {
            checkHealth();
        });
    </script>
@endpush

@if($vendor)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const validateBtn = document.getElementById('validateBtn');
            const resultsDiv = document.getElementById('validation-results');

            // Replace your existing validateBtn click handler
            validateBtn.addEventListener('click', async function () {
                // Show loading state
                resultsDiv.classList.remove('d-none');
                resultsDiv.innerHTML = `
                                        <div class="alert alert-info">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <span class="ms-2">Processing document...</span>
                                        </div>
                                    `;
                validateBtn.disabled = true;

                try {
                    // Call the new endpoint that handles everything server-side
                    const response = await axios.post(
                        '{{ route("api.vendor-validation.validate-existing", $vendor->vendor_id) }}',
                        {}, // No need to send any data
                        {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        }
                    );

                    // Process the response
                    const data = response.data;

                    // Display results
                    let resultsHTML = '';

                    if (data.valid) {
                        resultsHTML = `
    <div class="alert alert-success">
    <i class="bi bi-check-circle-fill me-2"></i> ${data.message || 'Document is valid'}
    </div>
    `;
                        // Auto-select approved radio
                        document.getElementById('status-approved').checked = true;
                    } else {
                        resultsHTML = `
    <div class="alert alert-danger">
    <i class="bi bi-x-circle-fill me-2"></i> ${data.message || 'Document is invalid'}
    </div>
    `;
                        // Auto-select rejected radio
                        document.getElementById('status-rejected').checked = true;
                    }

                    // Add validation details if present
                    if (data.validationResults) {
                        resultsHTML += '<div class="mt-3"><h6>Validation Details</h6><ul class="list-group">';

                        for (const [key, value] of Object.entries(data.validationResults)) {
                            const icon = value ? 'check-circle-fill text-success' : 'x-circle-fill text-danger';
                            const label = formatCheckName(key);

                            resultsHTML += `
    <li class="list-group-item d-flex justify-content-between align-items-center">
    ${label}
    <i class="bi bi-${icon}"></i>
    </li>
    `;
                        }

                        resultsHTML += '</ul></div>';
                    }

                    resultsDiv.innerHTML = resultsHTML;

                    // Update validation history if that function exists
                    if (typeof loadValidationHistory === 'function') {
                        loadValidationHistory('{{ $vendor->vendor_id }}');
                    }

                } catch (error) {
                    console.error('Validation error:', error);

                    resultsDiv.innerHTML = `
    <div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    Error validating document: ${error.response?.data?.message || error.message || 'Unknown error'}
    </div>
    `;
                } finally {
                    validateBtn.disabled = false;
                }
            });
        }
    </script>
@endif

{{-- Add Toast Notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div id="toast-body" class="toast-body"></div>
    </div>
</div>