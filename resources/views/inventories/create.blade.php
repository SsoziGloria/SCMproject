{{-- filepath: /Users/user/chocolate-scm/resources/views/inventories/create.blade.php --}}
@extends('supplier.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Add Inventory Record</h4>
                </div>
                <div class="card-body">

                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form action="{{ route('inventories.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="product_selector" class="form-label">Select Product</label>
                            <select name="product_selector" id="product_selector" class="form-control" required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" {{
                                    old('product_selector')==$product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (ID: {{ $product->id }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="product_id" class="form-label">Product ID</label>
                                <input type="text" id="product_id" name="product_id" class="form-control"
                                    value="{{ old('product_id') }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" id="product_name" name="product_name" class="form-control"
                                    value="{{ old('product_name') }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control"
                                value="{{ old('quantity') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control"
                                value="{{ old('location') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="expiration_date" class="form-label">Expiration Date</label>
                            <input type="date" name="expiration_date" id="expiration_date" class="form-control"
                                value="{{ old('expiration_date') }}" required>
                        </div>

                        @if(isset($suppliers) && count($suppliers) > 0)
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control">
                                <option value="">Select a supplier (optional)</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id')==$supplier->
                                    supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
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

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save Inventory</button>
                        </div>
                    </form>
                </div>
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

                // Set initial values if a product is already selected
                updateProductDetails();

                // Update when selection changes
                productSelector.addEventListener('change', updateProductDetails);
            });
</script>
@endpush

@endsection