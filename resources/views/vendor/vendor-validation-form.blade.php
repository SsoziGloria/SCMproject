<!DOCTYPE html>
<html>

<head>
    <title>Vendor Document Validation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .validation-details {
            margin-top: 10px;
        }

        .validation-details ul {
            margin: 5px 0;
        }

        .check-pass {
            color: #28a745;
        }

        .check-fail {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <h1>Vendor Document Validation</h1>

    <!-- Service Health Check -->
    <div>
        <button onclick="checkHealth()">Check Validation Service Health</button>
        <div id="health-status"></div>
    </div>

    <hr>

    <!-- Validation Form -->
    <form id="validation-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="vendor_id">Select Vendor:</label>
            <select id="vendor_id" name="vendor_id" required>
                <option value="">Select a vendor...</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}">{{ $vendor->name }} ({{ $vendor->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="file">Upload PDF Document:</label>
            <input type="file" id="file" name="file" accept=".pdf" required>
        </div>

        <button type="submit">Validate Document</button>
    </form>

    <div id="result"></div>

    <!-- Validation History -->
    <div id="validation-history" style="margin-top: 40px;">
        <h3>Validation History</h3>
        <div id="history-content"></div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Handle form submission
        document.getElementById('validation-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');

            // Show loading state
            resultDiv.innerHTML = '<div class="result loading">Validating document, please wait...</div>';

            try {
                const response = await axios.post('/api/vendor-validation/validate', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                displayResult(response.data, 'success');
                loadValidationHistory(formData.get('vendor_id'));

            } catch (error) {
                if (error.response && error.response.data) {
                    displayResult(error.response.data, 'error');
                } else {
                    displayResult({ message: 'Network error occurred' }, 'error');
                }
            }
        });

        // Display validation result
        function displayResult(data, type) {
            const resultDiv = document.getElementById('result');
            let html = `<div class="result ${type}">`;

            html += `<h3>${data.success ? 'Validation Successful' : 'Validation Failed'}</h3>`;
            html += `<p><strong>Message:</strong> ${data.message}</p>`;

            if (data.validation_details) {
                html += '<div class="validation-details">';
                html += '<h4>Validation Details:</h4>';
                html += '<ul>';

                for (const [key, value] of Object.entries(data.validation_details)) {
                    const status = value ? 'check-pass' : 'check-fail';
                    const icon = value ? '✓' : '✗';
                    html += `<li class="${status}">${icon} ${formatCheckName(key)}: ${value}</li>`;
                }

                html += '</ul>';
                html += '</div>';
            }

            html += '</div>';
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
            statusDiv.innerHTML = '<div class="result loading">Checking service health...</div>';

            try {
                const response = await axios.get('http://localhost:8080/api/v1/vendor/health');
                const status = response.data.validation_service_status;
                const cssClass = status === 'UP' ? 'success' : 'error';

                statusDiv.innerHTML = `<div class="result ${cssClass}">
                    <strong>Validation Service Status:</strong> ${status}
                </div>`;

            } catch (error) {
                statusDiv.innerHTML = '<div class="result error">Error checking service health</div>';
            }
        }

        // Load validation history for selected vendor
        async function loadValidationHistory(vendorId) {
            if (!vendorId) return;

            const historyDiv = document.getElementById('history-content');
            historyDiv.innerHTML = '<div class="result loading">Loading validation history...</div>';

            try {
                const response = await axios.get(`/api/vendor-validation/vendor/${vendorId}/history`);
                const validations = response.data.validations;

                if (validations.length === 0) {
                    historyDiv.innerHTML = '<p>No validation history found.</p>';
                    return;
                }

                let html = '<table border="1" style="width: 100%; border-collapse: collapse;">';
                html += '<thead><tr><th>Date</th><th>File</th><th>Status</th><th>Actions</th></tr></thead>';
                html += '<tbody>';

                validations.forEach(validation => {
                    const date = new Date(validation.validated_at).toLocaleDateString();
                    const status = validation.is_valid ?
                        '<span class="check-pass">✓ Valid</span>' :
                        '<span class="check-fail">✗ Invalid</span>';

                    html += `<tr>
                        <td>${date}</td>
                        <td>${validation.original_filename}</td>
                        <td>${status}</td>
                        <td>
                            <button onclick="viewValidation(${validation.id})">View Details</button>
                            <button onclick="revalidate(${validation.id})">Revalidate</button>
                        </td>
                    </tr>`;
                });

                html += '</tbody></table>';
                historyDiv.innerHTML = html;

            } catch (error) {
                historyDiv.innerHTML = '<div class="result error">Error loading validation history</div>';
            }
        }

        // View validation details
        async function viewValidation(validationId) {
            try {
                const response = await axios.get(`/api/vendor-validation/validation/${validationId}`);
                const validation = response.data.validation;

                alert(`Validation Details:\n\nFile: ${validation.original_filename}\nStatus: ${validation.is_valid ? 'Valid' : 'Invalid'}\nMessage: ${validation.validation_message}\nDate: ${new Date(validation.validated_at).toLocaleString()}`);

            } catch (error) {
                alert('Error loading validation details');
            }
        }

        // Revalidate document
        async function revalidate(validationId) {
            if (!confirm('Are you sure you want to revalidate this document?')) return;

            try {
                const response = await axios.post(`/api/vendor-validation/validation/${validationId}/revalidate`);
                alert('Document revalidated successfully');

                // Refresh the history
                const vendorId = document.getElementById('vendor_id').value;
                if (vendorId) {
                    loadValidationHistory(vendorId);
                }

            } catch (error) {
                alert('Error revalidating document');
            }
        }

        // Load history when vendor changes
        document.getElementById('vendor_id').addEventListener('change', function () {
            const vendorId = this.value;
            if (vendorId) {
                loadValidationHistory(vendorId);
            } else {
                document.getElementById('history-content').innerHTML = '';
            }
        });

        // Check health on page load
        window.onload = function () {
            checkHealth();
        };
    </script>
</body>

</html>