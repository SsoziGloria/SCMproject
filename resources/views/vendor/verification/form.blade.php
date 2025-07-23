@extends(auth()->user()->role . '.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Vendor Verification</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Before you can access advanced features, we need to verify your business details.
                        Please complete this form and upload the required documentation.
                    </div>

                    <form action="{{ route('vendor.verification.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Business Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name*</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                    value="{{ old('company_name', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person*</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    value="{{ old('contact_person', auth()->user()->name ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address*</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number*</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Business Address*</label>
                                <textarea class="form-control" id="address" name="address" rows="2"
                                    required>{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country*</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="Uganda" {{ old('country', 'Uganda' )=='Uganda' ? 'selected' : '' }}>
                                        Uganda</option>
                                    <option value="Kenya" {{ old('country', 'Uganda' )=='Kenya' ? 'selected' : '' }}>
                                        Kenya</option>
                                    <option value="Tanzania" {{ old('country', 'Uganda' )=='Tanzania' ? 'selected' : ''
                                        }}>Tanzania</option>
                                    <option value="Rwanda" {{ old('country', 'Uganda' )=='Rwanda' ? 'selected' : '' }}>
                                        Rwanda</option>
                                    <option value="Burundi" {{ old('country', 'Uganda' )=='Burundi' ? 'selected' : ''
                                        }}>Burundi</option>
                                </select>
                            </div>
                            {{-- <div class="col-md-6">
                                <label for="certification" class="form-label">Certification (if any)</label>
                                <input type="text" class="form-control" id="certification" name="certification"
                                    value="{{ old('certification') }}" placeholder="e.g., ISO 9001, HACCP">
                            </div> --}}
                        </div>

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Financial Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name*</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name"
                                    value="{{ old('bank_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number*</label>
                                <input type="text" class="form-control" id="account_number" name="account_number"
                                    value="{{ old('account_number') }}" required>
                            </div>
                        </div>

                        {{-- <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monthly_revenue" class="form-label">Estimated Monthly Revenue (UGX)*</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" class="form-control" id="monthly_revenue"
                                        name="monthly_revenue" value="{{ old('monthly_revenue') }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="revenue" class="form-label">Annual Revenue (UGX)</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" class="form-control" id="revenue" name="revenue"
                                        value="{{ old('revenue') }}" min="0">
                                </div>
                            </div>
                        </div> --}}

                        <h5 class="mt-4 mb-3 border-bottom pb-2">Documentation</h5>

                        <div class="mb-4">
                            <label for="verification_document" class="form-label">Validation Form*</label>
                            <input type="file" class="form-control" id="verification_document"
                                name="verification_document" accept=".pdf" required>
                            <div class="form-text">Please upload your validation documents in PDF format.
                                Ensure there is evidence of certification and financial statements.
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">
                                I confirm that all information provided is accurate, and I consent to verification
                                checks.
                            </label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit for Verification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection