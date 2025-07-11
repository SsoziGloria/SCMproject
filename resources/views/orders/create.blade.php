@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 tracking-wide mb-4 border-b pb-2">
    Submit Order
</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('orders.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Product:</label>
        <select name="product_id" class="form-control" required>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Submit Order</button>
</form>
@endsection