@extends(auth()->user()->role . '.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Create New Shipment</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shipments.index') }}">Shipments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Shipment</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Shipments
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
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Shipment Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipments.store') }}" method="POST">
                        @csrf

                        <!-- Shipment Type -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Shipment Type</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-check-card">
                                            <input class="form-check-input" type="radio" name="type" id="type_orders" 
                                                   value="orders" {{ old('type', $type ?? 'orders') === 'orders' ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 p-3 border rounded" for="type_orders">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-box-seam text-primary me-3 fs-4"></i>
                                                    <div>
                                                        <strong>Order Shipment</strong>
                                                        <div class="text-muted small">Create shipment for existing order</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-check-card">
                                            <input class="form-check-input" type="radio" name="type" id="type_suppliers" 
                                                   value="suppliers" {{ old('type') === 'suppliers' ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 p-3 border rounded" for="type_suppliers">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-truck text-success me-3 fs-4"></i>
                                                    <div>
                                                        <strong>Supplier Shipment</strong>
                                                        <div class="text-muted small">Incoming stock from supplier</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Shipment Section -->
                        <div id="order-section" class="shipment-section">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="order_id" class="form-label">Select Order</label>
                                    @if($orders->count() > 0)
                                    <select class="form-select @error('order_id') is-invalid @enderror" 
                                            id="order_id" name="order_id">
                                        <option value="">Choose an order to ship...</option>
                                        @foreach($orders as $order)
                                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                            {{ $order->order_number }} - {{ $order->customer_name }} ({{ $order->formatted_total }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Select an order that hasn't been shipped yet</div>
                                    @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>No orders available for shipment.</strong><br>
                                        All orders are either already shipped, delivered, or cancelled. 
                                        <a href="{{ route('orders.index') }}" class="alert-link">View all orders</a> 
                                        or create a supplier shipment instead.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Shipment Section -->
                        <div id="supplier-section" class="shipment-section" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" name="supplier_id">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ $product->formatted_price }}
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
                                           id="quantity" name="quantity" value="{{ old('quantity') }}" min="1"
                                           placeholder="Enter quantity">
                                    @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="expected_delivery" class="form-label">Expected Delivery</label>
                                    <input type="date" class="form-control @error('expected_delivery') is-invalid @enderror" 
                                           id="expected_delivery" name="expected_delivery" 
                                           value="{{ old('expected_delivery', now()->addDays(3)->format('Y-m-d')) }}">
                                    @error('expected_delivery')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Common Expected Delivery for Order Shipments -->
                        <div id="order-delivery-section" class="shipment-section">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="order_expected_delivery" class="form-label">Expected Delivery</label>
                                    <input type="date" class="form-control @error('expected_delivery') is-invalid @enderror" 
                                           id="order_expected_delivery" name="expected_delivery" 
                                           value="{{ old('expected_delivery', now()->addDays(3)->format('Y-m-d')) }}">
                                    @error('expected_delivery')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Estimated delivery date for the order</div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about this shipment...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Create Shipment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-card .form-check-input:checked + .form-check-label {
    background-color: var(--bs-primary-bg-subtle);
    border-color: var(--bs-primary);
}

.shipment-section {
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const orderSection = document.getElementById('order-section');
    const supplierSection = document.getElementById('supplier-section');
    const orderDeliverySection = document.getElementById('order-delivery-section');
    const ordersAvailable = {{ $orders->count() > 0 ? 'true' : 'false' }};

    // If no orders are available, default to suppliers and disable orders option
    if (!ordersAvailable) {
        document.getElementById('type_orders').disabled = true;
        document.getElementById('type_suppliers').checked = true;
        
        // Add visual indicator
        const orderLabel = document.querySelector('label[for="type_orders"]');
        orderLabel.classList.add('text-muted');
        orderLabel.style.opacity = '0.6';
    }

    function toggleSections() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;

        if (selectedType === 'orders') {
            // Show order sections
            orderSection.style.display = 'block';
            orderDeliverySection.style.display = 'block';
            supplierSection.style.display = 'none';

            // Enable order fields (only if orders are available)
            if (ordersAvailable) {
                document.getElementById('order_id').disabled = false;
                document.getElementById('order_expected_delivery').disabled = false;
            }

            // Disable supplier fields
            document.getElementById('supplier_id').disabled = true;
            document.getElementById('product_id').disabled = true;
            document.getElementById('quantity').disabled = true;
            document.getElementById('expected_delivery').disabled = true;

            // Clear supplier field values
            document.getElementById('supplier_id').value = '';
            document.getElementById('product_id').value = '';
            document.getElementById('quantity').value = '';
        } else {
            // Show supplier sections
            orderSection.style.display = 'none';
            orderDeliverySection.style.display = 'none';
            supplierSection.style.display = 'block';

            // Enable supplier fields
            document.getElementById('supplier_id').disabled = false;
            document.getElementById('product_id').disabled = false;
            document.getElementById('quantity').disabled = false;
            document.getElementById('expected_delivery').disabled = false;

            // Disable order fields
            if (document.getElementById('order_id')) {
                document.getElementById('order_id').disabled = true;
            }
            if (document.getElementById('order_expected_delivery')) {
                document.getElementById('order_expected_delivery').disabled = true;
            }

            // Clear order field values
            if (document.getElementById('order_id')) {
                document.getElementById('order_id').value = '';
            }
        }
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });
    
    // Initialize on page load
    toggleSections();
});
</script>
@endsection
