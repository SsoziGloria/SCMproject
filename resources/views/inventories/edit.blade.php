@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-primary">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Edit Inventory Record</h4>
                            <a href="{{ route('inventories.index') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('inventories.update', $inventory->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="product_selector" class="form-label">Select Product</label>
                                <select name="product_selector" id="product_selector" class="form-control" required>
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" {{ $inventory->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (ID: {{ $product->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="product_id" class="form-label">Product ID</label>
                                    <input type="text" id="product_id" name="product_id" class="form-control"
                                        value="{{ $inventory->product_id }}" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" id="product_name" name="product_name" class="form-control"
                                        value="{{ $inventory->product_name }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ $inventory->quantity }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" name="unit" id="unit" class="form-control"
                                        value="{{ $inventory->unit }}" placeholder="e.g., pcs, kg, boxes">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="batch_number" class="form-label">Batch Number</label>
                                    <input type="text" name="batch_number" id="batch_number" class="form-control"
                                        value="{{ $inventory->batch_number }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" name="location" id="location" class="form-control"
                                        value="{{ $inventory->location }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiration_date" class="form-label">Expiration Date</label>
                                    <input type="date" name="expiration_date" id="expiration_date" class="form-control"
                                        value="{{ $inventory->expiration_date?->format('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="available" {{ $inventory->status == 'available' ? 'selected' : '' }}>
                                            Available</option>
                                        <option value="reserved" {{ $inventory->status == 'reserved' ? 'selected' : '' }}>
                                            Reserved</option>
                                        <option value="damaged" {{ $inventory->status == 'damaged' ? 'selected' : '' }}>
                                            Damaged</option>
                                        <option value="expired" {{ $inventory->status == 'expired' ? 'selected' : '' }}>
                                            Expired</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-control">
                                    <option value="">Select a supplier (optional)</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $inventory->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    Delete Record
                                </button>
                                <button type="submit" class="btn btn-primary px-4">Update Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this inventory record for
                        <strong>{{ $inventory->product_name }}</strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Record</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productSelector = document.getElementById('product_selector');
                const productIdInput = document.getElementById('product_id');
                const productNameInput = document.getElementById('product_name');

                // Function to update product details based on selection
                function updateProductDetails() {
                    if (productSelector.value) {
                        const selectedOption = productSelector.options[productSelector.selectedIndex];
                        productIdInput.value = productSelector.value; // Set product ID
                        productNameInput.value = selectedOption.getAttribute('data-name'); // Set product name
                    } else {
                        productIdInput.value = '';
                        productNameInput.value = '';
                    }
                }

                // Update when selection changes
                productSelector.addEventListener('change', updateProductDetails);
            });
        </script>
    @endpush

@endsection