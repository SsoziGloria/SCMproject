{{-- filepath: resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Order Details</h2>
        <div class="card">
            <div class="card-body">
                <p><strong>Order ID:</strong> {{ $order->id }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Product:</strong> {{ $order->product ? $order->product->name : 'N/A' }}</p>
                <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Back to Orders</a>
            </div>
        </div>
    </div>
@endsection