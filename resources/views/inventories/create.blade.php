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
    

                <form action="{{ route('inventories.store') }}" method="POST">

                @csrf

                <div class="form-group">
                <label for="product_id">Product ID</label><br>
                <input type="text" name="product_id" id="product_id" class="form-control" required>
                <br>
                </div>

        <div class="form-group">
            <label for="product_id">Product</label><br>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->name }}" data-name="{{ $product->name }}">
                        {{ $product->name }} (ID: {{ $product->id }})
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="product_name" id="product_name">
        </div>
        <br>

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
        

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Save Inventory</button>
        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('product_id');
            const nameInput = document.getElementById('product_name');
            select.addEventListener('change', function () {
                const selected = select.options[select.selectedIndex];
                nameInput.value = selected.getAttribute('data-name') || '';
            });
            // Set initial value if needed
            if (select.value) {
                const selected = select.options[select.selectedIndex];
                nameInput.value = selected.getAttribute('data-name') || '';
            }
        });
    </script>
@endsection