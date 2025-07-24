@extends(auth()->user()->role . '.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-{{ $type === 'suppliers' ? 'box-arrow-in-down' : 'truck' }} me-2"></i>
                {{ $type === 'suppliers' ? 'Supplier Shipments' : 'Order Tracking' }}
            </h1>
            <p class="mb-0 text-muted">
                {{ $type === 'suppliers' ? 'Track incoming inventory from suppliers' : 'Track customer order deliveries' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->role === 'admin' || ($type === 'suppliers' && Auth::user()->role === 'supplier'))
            <a href="{{ route('shipments.create', ['type' => $type]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Shipment
            </a>
            @endif
        </div>
    </div>

    <!-- Type Toggle -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="btn-group w-100" role="group">
                <a href="{{ route('shipments.index', ['type' => 'suppliers']) }}" 
                   class="btn {{ $type === 'suppliers' ? 'btn-info' : 'btn-outline-info' }}">
                    <i class="bi bi-box-arrow-in-down me-2"></i>
                    Supplier Shipments
                    <span class="badge bg-{{ $type === 'suppliers' ? 'light text-dark' : 'info' }} ms-2">
                        {{ $supplierShipmentsCount }}
                    </span>
                </a>
                <a href="{{ route('shipments.index', ['type' => 'orders']) }}" 
                   class="btn {{ $type === 'orders' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-truck me-2"></i>
                    Order Tracking
                    <span class="badge bg-{{ $type === 'orders' ? 'light text-dark' : 'success' }} ms-2">
                        {{ $orderShipmentsCount }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('shipments.index') }}" class="mb-4">
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search shipments..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="processing" {{ request('status')=='processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="in_transit" {{ request('status')=='in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivered" {{ request('status')=='delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>

                    @if($type === 'suppliers' && Auth::user()->role !== 'supplier')
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ request('supplier_id')==$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            <a href="{{ route('shipments.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Shipments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                {{ $type === 'suppliers' ? 'Supplier Shipments' : 'Order Tracking' }} List
            </h6>
            <span class="badge bg-secondary">{{ $shipments->total() }} shipments found</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Shipment #</th>
                            @if($type === 'suppliers')
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Supplier</th>
                            @else
                            <th>Order</th>
                            <th>Customer</th>
                            @endif
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Expected Delivery</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td>
                                <strong>{{ $shipment->shipment_number }}</strong>
                            </td>
                            
                            @if($type === 'suppliers')
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($shipment->product && $shipment->product->image)
                                    <img src="{{ asset('storage/' . $shipment->product->image) }}"
                                        alt="{{ $shipment->product->name }}" class="rounded me-2"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $shipment->product->name ?? 'Unknown Product' }}</div>
                                        @if($shipment->product && $shipment->product->product_id)
                                        <small class="text-muted">SKU: {{ $shipment->product->product_id }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($shipment->quantity) }}</span>
                            </td>
                            <td>
                                @if($shipment->supplier)
                                <div>
                                    <div class="fw-medium">{{ $shipment->supplier->name }}</div>
                                    @if($shipment->supplier->email)
                                    <small class="text-muted">{{ $shipment->supplier->email }}</small>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">Unknown Supplier</span>
                                @endif
                            </td>
                            @else
                            {{-- Order tracking columns --}}
                            <td>
                                @if($shipment->order)
                                <div>
                                    <a href="{{ route('orders.show', $shipment->order->id) }}" class="text-decoration-none fw-medium">
                                        #{{ $shipment->order->order_number }}
                                    </a>
                                    <div class="small text-muted">{{ $shipment->order->formatted_total }}</div>
                                </div>
                                @else
                                <span class="text-muted">No Order</span>
                                @endif
                            </td>
                            <td>
                                @if($shipment->order)
                                <div>
                                    <div class="fw-medium">{{ $shipment->order->customer_name }}</div>
                                    @if($shipment->order->customer_email)
                                    <small class="text-muted">{{ $shipment->order->customer_email }}</small>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">Unknown Customer</span>
                                @endif
                            </td>
                            @endif
                            
                            <td>
                                <span class="badge bg-{{ $shipment->status_badge }}">
                                    <i class="bi bi-{{ $shipment->status_icon }} me-1"></i>
                                    {{ ucfirst($shipment->status) }}
                                </span>
                                @if($shipment->isOverdue())
                                <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </td>
                            
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $shipment->status_badge }}" 
                                         style="width: {{ $shipment->progress_percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $shipment->progress_percentage }}%</small>
                            </td>
                            
                            <td>
                                @if($shipment->expected_delivery)
                                <div>
                                    <div class="{{ $shipment->isOverdue() ? 'text-danger fw-medium' : '' }}">
                                        {{ $shipment->expected_delivery->format('M j, Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $shipment->expected_delivery->diffForHumans() }}
                                    </small>
                                </div>
                                @else
                                <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                            
                            <td>
                                <div>{{ $shipment->created_at->format('M j, Y') }}</div>
                                <small class="text-muted">{{ $shipment->created_at->format('g:i A') }}</small>
                            </td>
                            
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('shipments.show', $shipment->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'supplier' && $shipment->supplier_id === Auth::user()->id))
                                    <a href="{{ route('shipments.edit', $shipment->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    
                                    <!-- Quick Status Updates -->
                                    @if($shipment->status !== 'delivered')
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($shipment->status === 'processing')
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $shipment->id }}, 'shipped')">
                                                <i class="bi bi-box-seam me-2"></i>Mark as Shipped
                                            </a></li>
                                            @endif
                                            @if($shipment->status === 'shipped')
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $shipment->id }}, 'in_transit')">
                                                <i class="bi bi-truck me-2"></i>Mark in Transit
                                            </a></li>
                                            @endif
                                            @if($shipment->status === 'in_transit')
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $shipment->id }}, 'delivered')">
                                                <i class="bi bi-check-circle me-2"></i>Mark as Delivered
                                            </a></li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $type === 'suppliers' ? '8' : '8' }}" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-{{ $type === 'suppliers' ? 'box-arrow-in-down' : 'truck' }} display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No {{ $type === 'suppliers' ? 'supplier shipments' : 'order tracking' }} found</h5>
                                    <p class="text-muted mb-3">
                                        {{ $type === 'suppliers' ? 'No incoming shipments from suppliers found.' : 'No order deliveries to track found.' }}
                                    </p>
                                    @if(Auth::user()->role === 'admin' || ($type === 'suppliers' && Auth::user()->role === 'supplier'))
                                    <a href="{{ route('shipments.create', ['type' => $type]) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Create New Shipment
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Showing {{ $shipments->firstItem() ?? 0 }} to {{ $shipments->lastItem() ?? 0 }} 
            of {{ $shipments->total() }} results
        </div>
        {{ $shipments->appends(request()->query())->links() }}
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateStatus(shipmentId, status) {
    if (confirm('Are you sure you want to update the shipment status?')) {
        fetch(`/shipments/${shipmentId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    }
}
</script>
@endsection
