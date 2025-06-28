@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Add Inventory Record</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventories.store') }}" method="POST" class="inventory-form">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product ID</label>
                            <input type="number" name="product_id" id="product_id" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" name="product_name" id="product_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="expiration_date" class="form-label">Expiration Date</label>
                            <input type="date" name="expiration_date" id="expiration_date" class="form-control" required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save Inventory</button>
                            <a href="{{ route('inventories.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection