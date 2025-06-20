@extends('layouts.app')

@section('content')
    <h2>Add New Inventory Record</h2>

    <form action="{{ route('inventories.store') }}" method="POST">
        @csrf
        <label>Product ID:</label>
        <input type="number" name="product_id" required><br>
        <br>
        <label>Product Name:</label>
        <input type="text" name="product_name" required><br>
        <br>
        <label>Quantity:</label>
        <input type="text" name="quantity" required><br>
        <br>
        <label>Location:</label>
        <input type="text" name="location" required><br>
        <br>
        <label>Expiration Date:</label>
        <input type="date" name="expiration_date" required><br>
        <br>

        <button type="submit">Save Inventory</button>
    </form>
@endsection
