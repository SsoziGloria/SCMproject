@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Inventory Adjustments</h2>
    <a href="{{ route('inventories.adjustments.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Add Adjustment
    </a>
    <div class="card shadow-sm border-info">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-info">
                    <tr>
                        <th>No</th>
                        <th>Inventory Item</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adjustment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $adjustment->inventory->product_name ?? 'N/A' }}
                                <span class="text-muted small">(Current: {{ $adjustment->inventory->quantity ?? 'N/A' }})</span>
                            </td>
                            <td>
                                <span class="badge {{ $adjustment->type == 'add' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($adjustment->type) }}
                                </span>
                            </td>
                            <td>{{ $adjustment->amount }}</td>
                            <td>{{ $adjustment->reason ?? '-' }}</td>
                            <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-5">
                                <div class="card bg-light border-0 shadow-sm d-inline-block">
                                    <div class="card-body">
                                        <span class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            No adjustments recorded.
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