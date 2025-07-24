@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Adjustments History</h5>
                    <div>
                        <a href="{{ route('inventories.adjustments.create') }}" class="btn btn-sm btn-primary me-2">
                            <i class="bi bi-plus"></i> New Adjustment
                        </a>
                        <a href="{{ route('inventories.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($adjustments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Adjustment Type</th>
                                    <th>Quantity Change</th>
                                    <th>Reason</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adjustments as $adjustment)
                                    <tr>
                                        <td>{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $adjustment->inventory->product_name ?? 'Unknown Product' }}</strong>
                                                    @if($adjustment->inventory)
                                                        <div class="small text-muted">
                                                            ID: {{ $adjustment->inventory->product_id }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $typeClass = [
                                                    'increase' => 'success',
                                                    'decrease' => 'danger',
                                                    'correction' => 'warning',
                                                    'damage' => 'dark',
                                                    'expiry' => 'secondary'
                                                ][$adjustment->adjustment_type] ?? 'primary';
                                            @endphp
                                            <span class="badge bg-{{ $typeClass }}">
                                                {{ ucfirst($adjustment->adjustment_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($adjustment->quantity_change > 0)
                                                <span class="text-success">+{{ $adjustment->quantity_change }}</span>
                                            @else
                                                <span class="text-danger">{{ $adjustment->quantity_change }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 250px;"
                                                data-bs-toggle="tooltip" title="{{ $adjustment->reason }}">
                                                {{ $adjustment->reason }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $adjustment->user_name ?? 'System' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                        <p class="mt-3 mb-0">No adjustment records found.</p>
                        <a href="{{ route('inventories.adjustments.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus"></i> Create First Adjustment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            });
        </script>
    @endpush

@endsection