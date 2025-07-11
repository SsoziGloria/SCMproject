@extends('admin.app')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 tracking-wide mb-4 border-b pb-2">
        Edit Order #{{ $order->order_number }}
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

    <form action="{{ route('orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Status:</label>
            <select name="status" class="form-control" required>
                @foreach(['pending', 'processing', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" @if($order->status == $status) selected @endif>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Payment Status:</label>
            <select name="payment_status" class="form-control" required>
                @foreach(['pending', 'paid', 'failed'] as $pstatus)
                    <option value="{{ $pstatus }}" @if($order->payment_status == $pstatus) selected @endif>
                        {{ ucfirst($pstatus) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Order Notes:</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="fw-bold">Order Items:</label>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Product #' . $item->product_id }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price, 0) }}</td>
                                <td class="text-end">{{ number_format($item->price * $item->quantity, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update Order</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
@endsection