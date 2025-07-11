<table>
    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Total</th>
            <th>Order Date</th>
            <th>Product(s)</th>
            <th>Quantity</th>
            <th>Unit Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->total_amount }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ $item->product->name ?? 'Product #' . $item->product_id }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->price }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>