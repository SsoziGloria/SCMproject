@extends(auth()->user()->role . '.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-{{ $type === 'incoming' ? 'box-arrow-in-down' : 'box-arrow-up' }} me-2"></i>
                {{ $type === 'incoming' ? 'Supplier Shipments' : 'Customer Shipments' }}
            </h1>
            <p class="mb-0 text-muted">Manage and track {{ $type }} shipments</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Quick Actions -->
            @if(auth()->user()->role === 'admin' || ($type === 'incoming' && auth()->user()->role === 'supplier'))
            <a href="{{ route('shipments.create', ['type' => $type]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New {{ ucfirst($type) }} Shipment
            </a>
            @endif
            
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-download me-2"></i>Export Data
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="toggleAutoRefresh()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Auto Refresh
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Type Toggle -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="btn-group w-100" role="group">
                <a href="{{ route('shipments.index', ['type' => 'incoming']) }}" 
                   class="btn {{ $type === 'incoming' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-box-arrow-in-down me-2"></i>
                    Incoming Shipments
                    <span class="badge bg-{{ $type === 'incoming' ? 'light text-dark' : 'primary' }} ms-2">
                        {{ $incomingCount ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('shipments.index', ['type' => 'outgoing']) }}" 
                   class="btn {{ $type === 'outgoing' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="bi bi-box-arrow-up me-2"></i>
                    Outgoing Shipments
                    <span class="badge bg-{{ $type === 'outgoing' ? 'light text-dark' : 'success' }} ms-2">
                        {{ $outgoingCount ?? 0 }}
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

                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                            @if($type === 'incoming')
                            <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Shipped</option>
                            @else
                            <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="in_transit" {{ request('status')=='in_transit' ? 'selected' : '' }}>In Transit</option>
                            @endif
                            <option value="delivered" {{ request('status')=='delivered' ? 'selected' : '' }}>Delivered</option>
                            @if($type === 'outgoing')
                            <option value="returned" {{ request('status')=='returned' ? 'selected' : '' }}>Returned</option>
                            @endif
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    @if($type === 'incoming' && Auth::user()->role !== 'supplier')
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id')==$supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if($type === 'outgoing' && Auth::user()->role !== 'supplier')
                    <div class="col-md-3">
                        <select name="order_id" class="form-select">
                            <option value="">All Orders</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ request('order_id')==$order->id ? 'selected' : '' }}>
                                #{{ $order->order_number }} - {{ $order->customer_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-3">
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
                {{ $type === 'incoming' ? 'Supplier Shipments' : 'Customer Shipments' }} List
            </h6>
            <span class="badge bg-secondary">{{ $shipments->total() }} shipments found</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Shipment #</th>
                            @if($type === 'incoming')
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Supplier</th>
                            @else
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Tracking</th>
                            @endif
                            <th>Status</th>
                            <th>
                                {{ $type === 'incoming' ? 'Expected Delivery' : 'Estimated Delivery' }}
                            </th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td>
                                <strong>{{ $shipment->shipment_number }}</strong>
                                <span class="badge bg-{{ $shipment->shipment_type === 'incoming' ? 'info' : 'success' }} ms-2">
                                    <i class="bi bi-{{ $shipment->shipment_type === 'incoming' ? 'box-arrow-in-down' : 'box-arrow-up' }} me-1"></i>
                                    {{ ucfirst($shipment->shipment_type) }}
                                </span>
                            </td>
                            
                            @if($type === 'incoming')
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
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white" 
                                         style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($shipment->supplier->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $shipment->supplier->name }}</div>
                                        @if($shipment->supplier->email)
                                        <small class="text-muted">{{ $shipment->supplier->email }}</small>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">Unknown Supplier</span>
                                @endif
                            </td>
                            @else
                            {{-- Outgoing shipment columns --}}
                            <td>
                                @if($shipment->order)
                                <div>
                                    <a href="{{ route('orders.show', $shipment->order->id) }}" class="text-decoration-none fw-medium">
                                        #{{ $shipment->order->order_number }}
                                    </a>
                                    <div class="small text-muted">${{ number_format($shipment->order->total_amount, 2) }}</div>
                                </div>
                                @else
                                <span class="text-muted">No Order</span>
                                @endif
                            </td>
                            <td>
                                @if($shipment->order)
                                <div>
                                    <div class="fw-medium">{{ $shipment->order->customer_name }}</div>
                                    @if($shipment->order->email)
                                    <small class="text-muted">{{ $shipment->order->email }}</small>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">Unknown Customer</span>
                                @endif
                            </td>
                            <td>
                                @if($shipment->tracking_number)
                                <div>
                                    <code class="small">{{ $shipment->tracking_number }}</code>
                                    @if($shipment->carrier)
                                    <div class="small text-muted">{{ $shipment->carrier }}</div>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">No tracking</span>
                                @endif
                            </td>
                            @endif
                            
                            <td>
                                <span class="badge bg-{{ 
                                    $shipment->status === 'pending' ? 'warning' :
                                    ($shipment->status === 'shipped' ? 'info' :
                                    ($shipment->status === 'in_transit' ? 'primary' :
                                    ($shipment->status === 'delivered' ? 'success' :
                                    ($shipment->status === 'returned' ? 'danger' : 'secondary'))))
                                }}">
                                    <i class="bi bi-{{ 
                                        $shipment->status === 'pending' ? 'hourglass-split' :
                                        ($shipment->status === 'shipped' ? 'box-seam' :
                                        ($shipment->status === 'in_transit' ? 'truck' :
                                        ($shipment->status === 'delivered' ? 'check-circle' :
                                        ($shipment->status === 'returned' ? 'arrow-return-left' : 'x-circle'))))
                                    }} me-1"></i>
                                    {{ ucfirst($shipment->status) }}
                                </span>
                            </td>
                            
                            <td>
                                @if($shipment->estimated_delivery)
                                <div>
                                    <div class="{{ $shipment->estimated_delivery->isPast() && $shipment->status !== 'delivered' ? 'text-danger fw-medium' : '' }}">
                                        {{ $shipment->estimated_delivery->format('M j, Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $shipment->estimated_delivery->diffForHumans() }}
                                    </small>
                                </div>
                                @elseif($shipment->expected_delivery)
                                <div>
                                    <div class="{{ $shipment->expected_delivery->isPast() && $shipment->status !== 'delivered' ? 'text-danger fw-medium' : '' }}">
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
                                    @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'supplier' && $shipment->supplier_id === auth()->id()))
                                    <a href="{{ route('shipments.edit', $shipment->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->role === 'admin')
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteShipment({{ $shipment->id }}, '{{ $shipment->shipment_number }}')"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $type === 'incoming' ? '7' : '8' }}" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-{{ $type === 'incoming' ? 'box-arrow-in-down' : 'box-arrow-up' }} display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No {{ $type }} shipments found</h5>
                                    <p class="text-muted mb-3">
                                        @if($type === 'incoming')
                                        No supplier shipments match your current filters.
                                        @else
                                        No customer shipments match your current filters.
                                        @endif
                                    </p>
                                    @if(auth()->user()->role === 'admin' || ($type === 'incoming' && auth()->user()->role === 'supplier'))
                                    <a href="{{ route('shipments.create', ['type' => $type]) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Create {{ ucfirst($type) }} Shipment
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteShipmentModal" tabindex="-1" aria-labelledby="deleteShipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteShipmentModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete shipment <strong id="deleteShipmentNumber"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    This action cannot be undone. All shipment data and tracking information will be permanently removed.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteShipmentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Shipment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="bi bi-download me-2"></i>Export Shipments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="{{ route('shipments.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    @if(request('supplier_id'))
                    <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
                    @endif

                    @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="mb-3">
                        <label for="export_date_from" class="form-label">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="export_date_from" name="date_from"
                                value="{{ request('date_from') }}">
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control" id="export_date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="export_format" class="form-label">Format</label>
                        <select id="export_format" name="format" class="form-select">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="exportForm" class="btn btn-primary">
                    <i class="bi bi-download me-1"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function deleteShipment(shipmentId, shipmentNumber) {
    const confirmModal = new bootstrap.Modal(document.getElementById('deleteShipmentModal'));
    document.getElementById('deleteShipmentNumber').textContent = shipmentNumber;
    document.getElementById('deleteShipmentForm').action = `/shipments/${shipmentId}`;
    confirmModal.show();
}

// Auto-refresh for real-time status updates
let autoRefresh = false;
let refreshInterval;

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const button = document.querySelector('[onclick="toggleAutoRefresh()"]');
    
    if (autoRefresh) {
        button.innerHTML = '<i class="bi bi-pause-circle me-2"></i>Stop Auto Refresh';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-warning');
        
        // Refresh every 30 seconds
        refreshInterval = setInterval(() => {
            window.location.reload();
        }, 30000);
    } else {
        button.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Auto Refresh';
        button.classList.remove('btn-warning');
        button.classList.add('btn-outline-secondary');
        
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
