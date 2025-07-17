@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Adjustment Details</h5>
                            <a href="{{ route('inventories.adjustments') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Adjustments
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title border-bottom pb-2">Adjustment Information</h6>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4">ID</dt>
                                            <dd class="col-sm-8">{{ $adjustment->id }}</dd>

                                            <dt class="col-sm-4">Date/Time</dt>
                                            <dd class="col-sm-8">{{ $adjustment->created_at->format('M d, Y H:i:s') }}</dd>

                                            <dt class="col-sm-4">Type</dt>
                                            <dd class="col-sm-8">
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
                                            </dd>

                                            <dt class="col-sm-4">Change</dt>
                                            <dd
                                                class="col-sm-8 {{ $adjustment->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>
                                                    {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                                                </strong>
                                            </dd>

                                            <dt class="col-sm-4">Performed By</dt>
                                            <dd class="col-sm-8">{{ $adjustment->user_name ?? 'System' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title border-bottom pb-2">Product Information</h6>
                                        <dl class="row mb-0">
                                            @if($adjustment->inventory)
                                                <dt class="col-sm-4">Product</dt>
                                                <dd class="col-sm-8">{{ $adjustment->inventory->product_name }}</dd>

                                                <dt class="col-sm-4">Product ID</dt>
                                                <dd class="col-sm-8">{{ $adjustment->inventory->product_id }}</dd>

                                                <dt class="col-sm-4">Location</dt>
                                                <dd class="col-sm-8">{{ $adjustment->inventory->location ?? 'N/A' }}</dd>

                                                <dt class="col-sm-4">Current Qty</dt>
                                                <dd class="col-sm-8">{{ $adjustment->inventory->quantity }}
                                                    {{ $adjustment->inventory->unit }}</dd>
                                            @else
                                                <dd class="col-sm-12 text-center text-muted py-4">
                                                    Product information not available.
                                                    <br>
                                                    <small>(The inventory record may have been deleted)</small>
                                                </dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Reason for Adjustment</h6>
                                <p class="mb-0">{{ $adjustment->reason }}</p>
                            </div>
                        </div>

                        @if($adjustment->notes)
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Additional Notes</h6>
                                    <p class="mb-0">{{ $adjustment->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection