@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center"><i class="bi bi-person-plus"></i> New Supplier Requests</h2>
    <div class="card shadow-sm border-warning">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->company ?? '-' }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-5">
                                <div class="alert alert-info mb-0 shadow-sm d-inline-block">
                                    <i class="bi bi-info-circle"></i> No new supplier requests found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection