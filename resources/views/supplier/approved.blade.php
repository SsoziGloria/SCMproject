@extends(auth()->user()->role . '.app')

@section('content')
<div class="pagetitle">
    <h1>Manage Suppliers</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Suppliers</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Approved Suppliers</h5>

            <!-- Filter and Search Form -->
            <form method="GET" action="{{-- route('admin.suppliers.approved') --}}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search"
                            placeholder="Search by name, email, or company..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Supplier / Company</th>
                            <th scope="col">Account Status</th>
                            <th scope="col">Products</th>
                            @if (auth()->user()->role === 'admin')
                            <th scope="col" class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $supplier->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $supplier->company ?? $supplier->company_name ?? 'N/A'
                                    }}</div>
                            </td>
                            <td>
                                @if($supplier->user)
                                <span class="badge bg-{{ $supplier->user->is_active ? 'success' : 'danger' }}">
                                    <i
                                        class="bi bi-{{ $supplier->user->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $supplier->user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @else
                                <span class="badge bg-secondary">No User</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $supplier->products_count ?? 0 }}</span>
                            </td>
                            @if (auth()->user()->role === 'admin')
                            <td class="text-end">
                                @if($supplier->user)
                                <a href="{{ route('admin.users.show', $supplier->user->id) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-person-lines-fill"></i> View Details
                                </a>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-1"></i>
                                    <h5 class="mt-3">No Approved Suppliers Found</h5>
                                    <p>Approved suppliers will appear here once they complete verification.</p>
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