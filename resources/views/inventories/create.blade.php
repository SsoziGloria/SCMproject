@extends('layouts.app')

@section('content')
    <h2 style="text-align: center; margin-bottom: 20px; font-weight: bold;">Add Inventory Record</h2>
<br>

    <form action="{{ route('inventories.store') }}" method="POST" class="inventory-form>
        @csrf

<div class="form-group">
            <label for="product_id">Product ID:</label><br>
            <input type="number" name="product_id" id="product_id" required>
</div>            

<div class="form-group">
        <label>Product Name:</label>
        <input type="text" name="product_name" id="product_name" required><br>
        <br>
</div>

<div class="form-group">
        <label>Quantity:</label>
        <input type="text" name="quantity" id="quantity" required><br>
        <br>
</div>

<div class="form-group">
        <label>Location:</label>
        <input type="text" name="location" id="location" required><br>
        <br>
</div>

<div class="form-group">
        <label>Expiration Date:</label>
        <input type="date" name="expiration_date" id="expiration_date" required><br>
        <br>
</div>


<div class="text-center mt-3">
    <button type="submit" class="btn btn-primary">Save Inventory</button>
</div>

    </form>
@endsection
