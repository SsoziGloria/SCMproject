
@extends('layouts.app')

@section('content')

    <h2 style="text-align: center; margin-bottom: 20px; font-weight: bold;">Add Inventory Record</h2>
<br>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


 <form action="{{ route('inventories.store') }}" method="POST">
    
        @csrf

<div class="form-group">
            <label for="product_id">Product ID</label><br>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
</div>            
<br>


<div class="form-group">
        <label>Quantity</label>
        <input type="text" name="quantity" id="quantity" required><br>
        <br>
</div>

<div class="form-group">
        <label>Location</label>
        <input type="text" name="location" id="location" required><br>
        <br>
</div>

<div class="form-group">
        <label>Expiration Date</label>
        <input type="date" name="expiration_date" id="expiration_date" required><br>
        <br>
</div>


<div class="text-center mt-3">
    <button type="submit" class="btn btn-primary">Save Inventory</button>
</div>

    </form>
@endsection
