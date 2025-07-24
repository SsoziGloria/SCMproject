@extends(auth()->user()->role . '.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Edit Shipment #{{ $shipment->shipment_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shipments.index') }}">Shipments</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shipments.show', $shipment->id) }}">{{ $shipment->shipment_number }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Shipment
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>Edit Shipment Information
                    </h5>
                    <div>
                        <span class="badge bg-{{ $shipment->shipment_type === 'incoming' ? 'info' : 'success' }}">
                            {{ ucfirst($shipment->shipment_type) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipments.update', $shipment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Shipment Number (Read-only) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="shipment_number" class="form-label">Shipment Number</label>
                                <input type="text" class="form-control" id="shipment_number" 
                                       value="{{ $shipment->shipment_number }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="shipped" {{ $shipment->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    @if($shipment->shipment_type === 'outgoing')
                                    <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    @endif
                                    <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    @if($shipment->shipment_type === 'outgoing')
                                    <option value="returned" {{ $shipment->status === 'returned' ? 'selected' : '' }}>Returned</option>
                                    @endif
                                    <option value="cancelled" {{ $shipment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($shipment->shipment_type === 'incoming')
                        <!-- Supplier Shipment Fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $shipment->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select @error('product_id') is-invalid @enderror" 
                                        id="product_id" name="product_id">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $shipment->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ $shipment->quantity }}" min="1">
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="expected_delivery" class="form-label">Expected Delivery</label>
                                <input type="date" class="form-control @error('expected_delivery') is-invalid @enderror" 
                                       id="expected_delivery" name="expected_delivery" 
                                       value="{{ $shipment->expected_delivery ? $shipment->expected_delivery->format('Y-m-d') : '' }}">
                                @error('expected_delivery')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        @if($shipment->shipment_type === 'outgoing')
                        <!-- Customer Shipment Fields -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="order_info" class="form-label">Order Information</label>
                                <div class="input-group">
                                    <span class="input-group-text">Order #</span>
                                    <input type="text" class="form-control" 
                                           value="{{ $shipment->order ? $shipment->order->order_number : 'N/A' }}" readonly>
                                    @if($shipment->order)
                                    <span class="input-group-text">{{ $shipment->order->customer_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                       id="tracking_number" name="tracking_number" value="{{ $shipment->tracking_number }}">
                                @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="carrier" class="form-label">Carrier</label>
                                <select class="form-select @error('carrier') is-invalid @enderror" 
                                        id="carrier" name="carrier">
                                    <option value="">Select Carrier</option>
                                    <option value="DHL" {{ $shipment->carrier === 'DHL' ? 'selected' : '' }}>DHL</option>
                                    <option value="FedEx" {{ $shipment->carrier === 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                    <option value="UPS" {{ $shipment->carrier === 'UPS' ? 'selected' : '' }}>UPS</option>
                                    <option value="USPS" {{ $shipment->carrier === 'USPS' ? 'selected' : '' }}>USPS</option>
                                    <option value="PostNet" {{ $shipment->carrier === 'PostNet' ? 'selected' : '' }}>PostNet</option>
                                    <option value="Other" {{ $shipment->carrier === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('carrier')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Customer Address</label>
                            <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                      id="customer_address" name="customer_address" rows="3">{{ $shipment->customer_address }}</textarea>
                            @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Common Fields -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about this shipment...">{{ $shipment->notes }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Delivery Dates -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="estimated_delivery_datetime" class="form-label">Estimated Delivery</label>
                                <input type="datetime-local" class="form-control @error('estimated_delivery') is-invalid @enderror" 
                                       id="estimated_delivery_datetime" name="estimated_delivery" 
                                       value="{{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('Y-m-d\TH:i') : '' }}">
                                @error('estimated_delivery')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipped_at" class="form-label">Shipped At</label>
                                <input type="datetime-local" class="form-control @error('shipped_at') is-invalid @enderror" 
                                       id="shipped_at" name="shipped_at" 
                                       value="{{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d\TH:i') : '' }}">
                                @error('shipped_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($shipment->status === 'delivered')
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="actual_delivery" class="form-label">Actual Delivery</label>
                                <input type="datetime-local" class="form-control @error('actual_delivery') is-invalid @enderror" 
                                       id="actual_delivery" name="actual_delivery" 
                                       value="{{ $shipment->actual_delivery ? $shipment->actual_delivery->format('Y-m-d\TH:i') : '' }}">
                                @error('actual_delivery')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shipments.show', $shipment->id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <div>
                                @if(auth()->user()->role === 'admin')
                                <button type="button" class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Shipment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                <p>Are you sure you want to delete shipment <strong>#{{ $shipment->shipment_number }}</strong>?</p>
                @if($shipment->shipment_type === 'outgoing' && $shipment->order)
                <p class="text-muted">
                    <small>Note: This will not affect the associated order (#{{ $shipment->order->order_number }}), 
                    only the shipment tracking record will be removed.</small>
                </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete Shipment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const status = document.getElementById('status');
    const shippedAtField = document.getElementById('shipped_at');
    
    function updateShippedAt() {
        if (['shipped', 'in_transit', 'delivered'].includes(status.value)) {
            if (!shippedAtField.value) {
                // Set current datetime if not already set
                const now = new Date();
                const isoString = now.toISOString().slice(0, 16);
                shippedAtField.value = isoString;
            }
        }
    }
    
    status.addEventListener('change', updateShippedAt);
});
</script>
@endsection
