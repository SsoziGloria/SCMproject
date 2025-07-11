@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-check-circle"></i> Approved Suppliers</h2>
    <div class="card shadow-sm border-success">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $supplier->name ?? '-' }}</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ $supplier->company ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">Approved</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5">
                                <div class="card bg-light border-0 shadow-sm d-inline-block">
                                    <div class="card-body">
                                        <span class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            No approved suppliers found.
                                        </span>
                                    </div>
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