@extends(auth()->user()->role . '.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('shipments.index', ['type' => $shipment->order_id ? 'orders' : 'suppliers']) }}">
                            {{ $shipment->order_id ? 'Order Tracking' : 'Supplier Shipments' }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ $shipment->shipment_number }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-{{ $shipment->order_id ? 'truck' : 'box-arrow-in-down' }} me-2"></i>
                Shipment {{ $shipment->shipment_number }}
            </h1>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'supplier' && $shipment->supplier_id === Auth::user()->id))
            <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit Shipment
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Shipment Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Shipment Details</h6>
                    <span class="badge bg-{{ $shipment->status_badge }} fs-6">
                        <i class="bi bi-{{ $shipment->status_icon }} me-1"></i>
                        {{ ucfirst($shipment->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium">Shipment Number:</td>
                                    <td>{{ $shipment->shipment_number }}</td>
                                </tr>
                                @if($shipment->order)
                                <tr>
                                    <td class="fw-medium">Order:</td>
                                    <td>
                                        <a href="{{ route('orders.show', $shipment->order->id) }}" class="text-decoration-none">
                                            #{{ $shipment->order->order_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Customer:</td>
                                    <td>{{ $shipment->order->customer_name }}</td>
                                </tr>
                                @endif
                                @if($shipment->supplier)
                                <tr>
                                    <td class="fw-medium">Supplier:</td>
                                    <td>{{ $shipment->supplier->name }}</td>
                                </tr>
                                @endif
                                @if($shipment->product)
                                <tr>
                                    <td class="fw-medium">Product:</td>
                                    <td>{{ $shipment->product->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Quantity:</td>
                                    <td>{{ number_format($shipment->quantity) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium">Created:</td>
                                    <td>{{ $shipment->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @if($shipment->shipped_at)
                                <tr>
                                    <td class="fw-medium">Shipped:</td>
                                    <td>{{ $shipment->shipped_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @endif
                                @if($shipment->delivered_at)
                                <tr>
                                    <td class="fw-medium">Delivered:</td>
                                    <td>{{ $shipment->delivered_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @endif
                                @if($shipment->expected_delivery)
                                <tr>
                                    <td class="fw-medium">Expected Delivery:</td>
                                    <td class="{{ $shipment->isOverdue() ? 'text-danger' : '' }}">
                                        {{ $shipment->expected_delivery->format('M j, Y') }}
                                        @if($shipment->isOverdue())
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($shipment->notes)
                    <div class="mt-3">
                        <h6 class="fw-medium">Notes:</h6>
                        <p class="text-muted">{{ $shipment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'supplier' && $shipment->supplier_id === Auth::user()->id) || (Auth::user()->role === 'retailer' && $shipment->order_id))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if($shipment->status !== 'delivered')
                    <form action="{{ route('shipments.update-status', $shipment) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        @if($shipment->status === 'processing')
                        <input type="hidden" name="status" value="shipped">
                        <button type="submit" class="btn btn-info w-100 mb-2">
                            <i class="bi bi-box-seam me-2"></i>Mark as Shipped
                        </button>
                        @elseif($shipment->status === 'shipped')
                        <input type="hidden" name="status" value="in_transit">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-truck me-2"></i>Mark in Transit
                        </button>
                        @elseif($shipment->status === 'in_transit')
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle me-2"></i>Mark as Delivered
                        </button>
                        @endif
                    </form>
                    @else
                    <div class="text-center">
                        <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        <p class="text-success mt-2 mb-0">Delivery Complete!</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Status & Actions -->
        <div class="col-lg-4">
            <!-- Progress Tracking -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Delivery Progress</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-{{ $shipment->status_badge }}" 
                             style="width: {{ $shipment->progress_percentage }}%">
                            {{ $shipment->progress_percentage }}%
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="timeline">
                        <div class="timeline-item {{ $shipment->status === 'processing' ? 'active' : ($shipment->progress_percentage > 25 ? 'completed' : '') }}">
                            <div class="timeline-marker">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Processing</h6>
                                <small class="text-muted">Order being prepared</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $shipment->status === 'shipped' ? 'active' : ($shipment->progress_percentage > 50 ? 'completed' : '') }}">
                            <div class="timeline-marker">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Shipped</h6>
                                <small class="text-muted">Package dispatched</small>
                                @if($shipment->shipped_at)
                                <div><small class="text-success">{{ $shipment->shipped_at->format('M j, g:i A') }}</small></div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $shipment->status === 'in_transit' ? 'active' : ($shipment->progress_percentage > 75 ? 'completed' : '') }}">
                            <div class="timeline-marker">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>In Transit</h6>
                                <small class="text-muted">On the way to destination</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $shipment->status === 'delivered' ? 'active completed' : '' }}">
                            <div class="timeline-marker">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Delivered</h6>
                                <small class="text-muted">Package received</small>
                                @if($shipment->delivered_at)
                                <div><small class="text-success">{{ $shipment->delivered_at->format('M j, g:i A') }}</small></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 2rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 1.25rem;
    top: 2.5rem;
    width: 2px;
    height: calc(100% - 1rem);
    background: #e9ecef;
}

.timeline-item.completed:not(:last-child)::before {
    background: #28a745;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 2.5rem;
    height: 2.5rem;
    background: #e9ecef;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.timeline-item.active .timeline-marker {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.timeline-item.active .timeline-content h6 {
    color: #007bff;
    font-weight: 600;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}
</style>

@endsection
