@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Adjustments Report</h5>
                    <div>
                        <a href="{{ route('inventories.adjustments.export', [
        'date_from' => $dateFrom,
        'date_to' => $dateTo
    ]) }}" class="btn btn-sm btn-success me-2">
                            <i class="bi bi-download"></i> Export to CSV
                        </a>
                        <a href="{{ route('inventories.adjustments') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Adjustments
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Report Parameters</h6>
                                <p class="mb-2">Showing adjustments from <strong>{{ $dateFrom }}</strong> to
                                    <strong>{{ $dateTo }}</strong></p>

                                <form action="{{ route('inventories.adjustments.report') }}" method="GET" class="mt-3">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <label for="date_from" class="col-form-label">From</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="date_from" name="date_from"
                                                class="form-control form-control-sm" value="{{ $dateFrom }}">
                                        </div>
                                        <div class="col-auto">
                                            <label for="date_to" class="col-form-label">To</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="date_to" name="date_to"
                                                class="form-control form-control-sm" value="{{ $dateTo }}">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-primary">Update Report</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-0">{{ $stats['total_count'] }}</h2>
                                <p class="mb-0">Total Adjustments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-0">{{ $stats['increases'] }}</h2>
                                <p class="mb-0">Stock Increases</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-0">{{ $stats['decreases'] }}</h2>
                                <p class="mb-0">Stock Decreases</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-0">{{ $stats['net_change'] }}</h2>
                                <p class="mb-0">Net Stock Change</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($adjustments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Change</th>
                                    <th>User</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adjustments as $adjustment)
                                    <tr>
                                        <td>{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $adjustment->inventory->product_name ?? 'Unknown Product' }}</td>
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
                                        <td class="{{ $adjustment->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                                        </td>
                                        <td>{{ $adjustment->user_name ?? 'System' }}</td>
                                        <td>{{ $adjustment->reason }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x display-1 text-muted"></i>
                        <p class="mt-3 mb-0">No adjustments found for the selected date range.</p>
                        <a href="{{ route('inventories.adjustments.report') }}" class="btn btn-outline-primary mt-3">
                            Reset Date Range
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection