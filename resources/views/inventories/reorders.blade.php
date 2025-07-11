@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-exclamation-triangle"></i> Reorders Needed</h2>
    <div class="card shadow-sm border-warning">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reorders as $inventory)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $inventory->product->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $inventory->quantity < $inventory->reorder_level ? 'bg-danger' : 'bg-success' }}">
                                    {{ $inventory->quantity }}
                                </span>
                            </td>
                            <td>{{ $inventory->reorder_level }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">Needs Reorder</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-5">
                                <div class="card bg-light border-0 shadow-sm d-inline-block">
                                    <div class="card-body">
                                        <span class="text-muted">
                                            <i class="bi bi-check-circle"></i>
                                            No products need reordering.
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