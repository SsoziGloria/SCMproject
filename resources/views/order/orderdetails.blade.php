@php
    if (Auth::check() && Auth::user()->role === 'admin') {
        $layout = 'admin.app';
    } elseif (Auth::check() && Auth::user()->role === 'retailer') {
        $layout = 'retailer.app';
    } elseif (Auth::check() && Auth::user()->role === 'supplier') {
        $layout = 'supplier.app';
    } else {
        $layout = 'layouts.app';
    }
@endphp

@extends($layout)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Order Details</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(isset($order))
        <main>
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Order #{{ $order->order_number }}</strong>
                </div>
                <div class="card-body">
                    <p><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Supplier:</strong> {{ $order->supplier->name ?? 'N/A' }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge 
                                    @if($order->status === 'pending') bg-warning
                                    @elseif($order->status === 'processing') bg-info
                                    @elseif($order->status === 'shipped') bg-primary
                                    @elseif($order->status === 'delivered') bg-success
                                    @elseif($order->status === 'cancelled') bg-danger
                                        @else bg-secondary
                                    @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Shipping Address:</strong> {{ $order->shipping_address ?? '-' }}</p>
                    <p><strong>Ordered At:</strong>
                        {{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '-' }}</p>
                    <p><strong>Delivered At:</strong>
                        {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') : '-' }}</p>
                </div>
            </div>
            {{-- Add more details or related items here if needed --}}
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    @else
                <div class="alert alert-danger">Order not found.</div>
            </main>
        @endif