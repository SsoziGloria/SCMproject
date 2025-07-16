@php
    if (Auth::check() && Auth::user()->role === 'admin') {
        $layout = 'admin.app';
    } elseif (Auth::check() && Auth::user()->role === 'retailer') {
        $layout = 'retailer.app';
    } elseif (Auth::check() && Auth::user()->role === 'supplier') {
        $layout = 'supplier.app';
    } else {
        $layout = 'user.app';
    }
@endphp

@extends($layout)

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Incoming Orders</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="alert alert-info">No incoming orders at the moment.</div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Supplier</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Ordered At</th>
                                    <th>Shipping Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                        <td>UGX{{ number_format($order->total_amount, 0) }}</td>
                                        <td>
                                            <span
                                                class="badge 
<<<<<<< Updated upstream
                                                                                                                                                                                                                        @if($order->status === 'pending') bg-warning
                                                                                                                                                                                                                        @elseif($order->status === 'processing') bg-info
                                                                                                                                                                                                                        @elseif($order->status === 'shipped') bg-primary
                                                                                                                                                                                                                        @elseif($order->status === 'delivered') bg-success
                                                                                                                                                                                                                        @elseif($order->status === 'cancelled') bg-danger
                                                                                                                                                                                                                            @else bg-secondary
                                                                                                                                                                                                                        @endif">
=======
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
                                        </td>
                                        <td>{{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '-' }}
                                        </td>
                                        <td>{{ $order->shipping_address ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
@endsection