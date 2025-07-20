@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-4">
        <h2>Inventory Details</h2>
        <div class="card">
            <div class="card-body">
                <p><strong>Product ID:</strong> {{ $inventory->product_id }}</p>
                <p><strong>Product Name:</strong> {{ $inventory->product_name }}</p>
                <p><strong>Quantity:</strong> {{ $inventory->quantity }}</p>
                <p><strong>Location:</strong> {{ $inventory->location }}</p>
                <p><strong>Expiration Date:</strong> {{ $inventory->expiration_date }}</p>
                <a href="{{ route('inventories.index') }}" class="btn btn-secondary">Back to Inventory</a>
            </div>
        </div>
    </div>
@endsection