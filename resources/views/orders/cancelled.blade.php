@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 tracking-wide mb-4 border-b pb-2">
    Cancelled Orders
</h2>

@include('orders._order_table', ['orders' => $orders])
@if($orders->isEmpty())
<<<<<<< Updated upstream
    <p>No returned orders.</p>
=======
<<<<<<< HEAD
    <p>No pending orders.</p>
=======
    <p>No returned orders.</p>
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
>>>>>>> Stashed changes
@else
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Order Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->product->name ?? 'N/A' }}</td>
            <td>{{ $order->quantity }}</td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->order_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection