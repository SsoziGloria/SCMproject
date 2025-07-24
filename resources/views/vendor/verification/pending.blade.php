@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Verification In Progress</h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="mb-3">Your verification is being processed</h3>
                        <p class="mb-4">
                            Thank you for submitting your vendor information. Our team is currently reviewing your details.
                            This process typically takes 1-2 business days.
                        </p>
                        <p>
                            <strong>Current Status:</strong>
                            <span class="badge bg-warning text-dark">{{ $vendor->validation_status ?? 'Pending' }}</span>
                        </p>

                        @if($vendor->visit_date)
                            <p class="mt-3">
                                <strong>Scheduled Visit Date:</strong>
                                {{ \Carbon\Carbon::parse($vendor->visit_date)->format('F j, Y') }}
                            </p>
                        @endif

                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Return to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection