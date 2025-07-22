@extends(auth()->user()->role . '.app')

@section('content')
<div class="pagetitle">
    <h1>Supplier Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Supplier Requests</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Pending Verification</h5>
                {{-- Optional: Add a search form if you need it --}}
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Company Name</th>
                            <th scope="col">Contact Person</th>
                            <th scope="col">Registered At</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $supplier->company_name ?? $supplier->company ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $supplier->email }}</div>
                            </td>
                            <td>{{ $supplier->name ?? 'N/A' }}</td>
                            <td>{{ $supplier->created_at->format('M d, Y') }}</td>
                            <td>
                                {{-- Dynamic status badge --}}
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock-history me-1"></i>
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                {{-- Direct link to the validation page for this specific vendor --}}
                                <a href="{{ route('admin.vendor-validation', ['vendor_id' => $supplier->vendor_id ?? $supplier->id]) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-shield-check"></i> Review Application
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-check fs-1"></i>
                                    <h5 class="mt-3">All Clear!</h5>
                                    <p>There are no new supplier requests awaiting verification.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($suppliers->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $suppliers->links() }}
            </div>
            @endif

        </div>
    </div>
</section>
@endsection