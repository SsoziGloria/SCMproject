@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order History - #{{ $order->order_number }}</h5>
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Order
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($statusHistory as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->status_color }}">
                                <i class="bi bi-{{ $history->status_icon }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0">Status changed to <strong>{{ ucfirst($history->status) }}</strong></h6>
                                    <span class="text-muted small">{{ $history->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="text-muted mb-0">{{ $history->notes ?? 'No additional notes' }}</p>
                                <p class="mb-0 small">By: {{ $history->user->name ?? 'System' }}</p>

                                @if(in_array($history->status, ['shipped', 'delivered']))
                                    <div class="alert alert-light mt-2 p-2">
                                        <h6 class="mb-1">Inventory Changes:</h6>
                                        <ul class="mb-0">
                                            @foreach($history->inventoryAdjustments as $adjustment)
                                                <li>
                                                    {{ $adjustment->inventory->product_name }} -
                                                    <span class="text-danger">-{{ abs($adjustment->quantity_change) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($history->status === 'cancelled' && $history->inventoryAdjustments->count() > 0)
                                    <div class="alert alert-light mt-2 p-2">
                                        <h6 class="mb-1">Inventory Restored:</h6>
                                        <ul class="mb-0">
                                            @foreach($history->inventoryAdjustments as $adjustment)
                                                <li>
                                                    {{ $adjustment->inventory->product_name }} -
                                                    <span class="text-success">+{{ $adjustment->quantity_change }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 15px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 25px;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            color: white;
            font-size: 16px;
            z-index: 2;
        }
    </style>
@endsection