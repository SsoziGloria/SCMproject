@extends(auth()->user()->role . '.app')

@extends($layout)

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Order Details</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(isset($order))
            {{-- Display order details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Order #{{ $order->order_number }}</strong>
                </div>
                <div class="card-body">
                    <p><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Supplier:</strong> {{ $order->supplier->name ?? 'N/A' }}</p>
                    <p><strong>Total Amount:</strong> UGX{{ number_format($order->total_amount, 0) }}</p>
                    <p><strong>Status:</strong>
<<<<<<< Updated upstream
                        <span
                            class="badge 
                                                                                                                            @if($order->status === 'pending') bg-warning
                                                                                                                            @elseif($order->status === 'processing') bg-info
                                                                                                                            @elseif($order->status === 'shipped') bg-primary
                                                                                                                            @elseif($order->status === 'delivered') bg-success
                                                                                                                            @elseif($order->status === 'cancelled') bg-danger
                                                                                                                                @else bg-secondary
                                                                                                                            @endif">
=======
                        <span class="badge 
                                                                                            @if($order->status === 'pending') bg-warning
                                                                                            @elseif($order->status === 'processing') bg-info
                                                                                            @elseif($order->status === 'shipped') bg-primary
                                                                                            @elseif($order->status === 'delivered') bg-success
                                                                                            @elseif($order->status === 'cancelled') bg-danger
                                                                                                @else bg-secondary
                                                                                            @endif">
>>>>>>> Stashed changes
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Shipping Address:</strong> {{ $order->shipping_address ?? '-' }}</p>
                    <p><strong>Ordered At:</strong>
                        {{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '-' }}</p>
                    <p><strong>Delivered At:</strong>
                        {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') : '-' }}
                    </p>
                </div>
            </div>
            {{-- Add more details or related items here if needed --}}
<<<<<<< Updated upstream
            <a href="{{{ route('orders.index') }}" class="btn btn-secondary">Back</a>
=======
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
>>>>>>> Stashed changes
        @else
                <div class="alert alert-danger">Order not found.</div>
            </div>
        @endif
@endsection