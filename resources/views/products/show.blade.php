
@extends('retailer.app')

@section('content')
<div class="container py-4">
    <h2>Product Details</h2>
    <div class="card">
        <div class="card-body">
            <h4>{{ $product->name }}</h4>
            <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
            <p><strong>Stock:</strong> {{ $product->stock }}</p>
            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
            <p><strong>Status:</strong>
                <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($product->status) }}
                </span>
            </p>
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>
</div>
@endsection