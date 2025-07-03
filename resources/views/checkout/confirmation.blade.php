@extends('user.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="card-title">Thank You for Your Order!</h2>
                        <p class="card-text">Your order has been placed successfully.</p>
                        <p class="card-text">Order Number: <strong>{{ $order->order_number }}</strong></p>
                        <p class="text-muted">A confirmation email has been sent to
                            {{ $order->email ?? 'your email address' }}
                        </p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <p class="mb-1">Order Number: {{ $order->order_number }}</p>
                                <p class="mb-1">Date: {{ $order->created_at->format('F j, Y') }}</p>
                                <p class="mb-1">Total: UGX {{ number_format($order->total_amount, 2) }}</p>
                                <p class="mb-1">Payment Method: {{ ucfirst(str_replace('_', ' ', $order->payment)) }}
                                </p>
                                <p class="mb-0">Status: <span class="badge bg-info">{{ ucfirst($order->status) }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Shipping Address</h6>
                                <p class="mb-0">{{ $order->shipping_address }}</p>
                                <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_country }}
                                </p>
                                <p class="mb-0">{{ $order->shipping_country }}</p>
                            </div>
                        </div>

                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">UGX {{ number_format($item->price, 0) }}</td>
                                            <td class="text-end">UGX {{ number_format($item->price * $item->quantity, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end">
                                            UGX {{ number_format($order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                        <td class="text-end">
                                            UGX {{ number_format($order->total_amount - $order->items->sum(function ($item) {
        return $item->price * $item->quantity; }), 0) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end">UGX {{ number_format($order->total_amount, 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($order->payment === 'bank_transfer')
                            <div class="alert alert-info mt-3">
                                <h6>Bank Transfer Instructions</h6>
                                <p class="mb-1">Please transfer the total amount of
                                    ${{ number_format($order->total_amount, 2) }} to:</p>
                                <p class="mb-1"><strong>Bank:</strong> Equity Bank</p>
                                <p class="mb-1"><strong>Account Name:</strong> Chocolate SCM Ltd</p>
                                <p class="mb-1"><strong>Account Number:</strong> XXXX-XXXX-XXXX-XXXX</p>
                                <p class="mb-0"><strong>Reference:</strong> {{ $order->order_number }}</p>
                            </div>
                        @endif

                        <div class="alert alert-warning mt-3">
                            <p class="mb-0"><i class="bi bi-info-circle"></i> For any questions regarding your order, please
                                contact our customer service at support@chocolatescm.com</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('shop.index') }}" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
@endsection