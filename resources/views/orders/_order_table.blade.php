@if($orders->isEmpty())
    <div class="alert alert-info text-center shadow-sm" style="max-width: 500px; margin: 30px auto;">
        <i class="bi bi-info-circle"></i> No orders found.
    </div>
@else
    <table class="table table-bordered table-striped table-hover align-middle shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
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
                    <td>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this order?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif