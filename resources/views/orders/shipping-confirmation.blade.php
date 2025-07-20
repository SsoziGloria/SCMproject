@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Shipping Confirmation - Order #{{ $order->order_number }}</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Please review the inventory status before confirming shipment.
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity Ordered</th>
                                <th>Available in Inventory</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $available = App\Models\Inventory::where('product_id', $item->product_id)
                                        ->where('status', 'available')
                                        ->sum('quantity');

                                    $status = match (true) {
                                        $available >= $item->quantity => 'In Stock',
                                        $available > 0 => 'Partial Stock',
                                        default => 'Out of Stock'
                                    };

                                    $statusClass = match ($status) {
                                        'In Stock' => 'success',
                                        'Partial Stock' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $available }}</td>
                                    <td><span class="badge bg-{{ $statusClass }}">{{ $status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    $canShip = $items->every(
                        fn($item) =>
                        App\Models\Inventory::where('product_id', $item->product_id)
                            ->where('status', 'available')
                            ->sum('quantity') >= $item->quantity
                    );
                @endphp

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary me-2">Go Back</a>

                    <form action="{{ route('orders.mark-as-shipped', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        @if(!$canShip)
                            <button type="button" class="btn btn-danger" disabled>
                                <i class="bi bi-exclamation-triangle"></i> Insufficient Inventory
                            </button>
                        @else
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-truck"></i> Confirm Shipment & Update Inventory
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection