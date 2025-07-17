@extends(auth()->user()->role . '.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Shipment Management</h1>
            <div>
                @if(Auth::user()->role !== 'supplier')
                    <a href="{{ route('shipments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create Shipment
                    </a>
                @endif
                <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal"
                    data-bs-target="#exportModal">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Shipments</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div>
                            <i class="bi bi-truck fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('shipments.index') }}">View Details</a>
                        <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['pending'] }}</div>
                        </div>
                        <div>
                            <i class="bi bi-hourglass-split fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link"
                            href="{{ route('shipments.index', ['status' => 'pending']) }}">View Details</a>
                        <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Shipped</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['shipped'] }}</div>
                        </div>
                        <div>
                            <i class="bi bi-box-seam fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link"
                            href="{{ route('shipments.index', ['status' => 'shipped']) }}">View Details</a>
                        <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card {{ $stats['overdue'] > 0 ? 'bg-danger' : 'bg-success' }} text-white mb-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                {{ $stats['overdue'] > 0 ? 'Overdue' : 'Delivered' }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $stats['overdue'] > 0 ? $stats['overdue'] : $stats['delivered'] }}</div>
                        </div>
                        <div>
                            <i
                                class="bi {{ $stats['overdue'] > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle' }} fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link"
                            href="{{ route('shipments.index', ['status' => $stats['overdue'] > 0 ? 'pending' : 'delivered']) }}">View
                            Details</a>
                        <div class="small text-white"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="m-0 font-weight-bold">Filters</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('shipments.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                    placeholder="Shipment #, Product...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">Status</span>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped
                                    </option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>
                        </div>

                        @if(Auth::user()->role !== 'supplier')
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Supplier</span>
                                    <select name="supplier_id" class="form-select">
                                        <option value="">All Suppliers</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">Sort</span>
                                <select name="sort" class="form-select">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First
                                    </option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                    </option>
                                    <option value="expected_delivery" {{ request('sort') == 'expected_delivery' ? 'selected' : '' }}>Expected Delivery</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Shipments Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Shipment List</h6>
                <span class="badge bg-secondary">{{ $shipments->total() }} shipments found</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Shipment #</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Supplier</th>
                                <th>Status</th>
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
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($shipment->product && $shipment->product->image)
                                                <img src="{{ asset('storage/' . $shipment->product->image) }}"
                                                    alt="{{ $shipment->product->name }}" class="me-2"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2" style="width: 40px; height: 40px;"></div>
                                            @endif
                                            <div>
                                                {{ $shipment->product->name ?? 'Unknown Product' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($shipment->quantity) }}</span>
                                    </td>
                                    <td>
                                        {{ $shipment->supplier->name ?? 'Unknown Supplier' }}
                                    </td>
                                    <td>
                                        @if($shipment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($shipment->status == 'shipped')
                                            <span class="badge bg-primary">Shipped</span>
                                        @elseif($shipment->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($shipment->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif

                                        @if($shipment->isOverdue())
                                            <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shipment->expected_delivery)
                                            <span class="{{ $shipment->isOverdue() ? 'text-danger' : '' }}">
                                                {{ $shipment->expected_delivery->format('M d, Y') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $shipment->expected_delivery->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $shipment->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('shipments.show', $shipment) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(Auth::user()->role !== 'supplier')
                                                <a href="{{ route('shipments.edit', $shipment) }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif

                                            <!-- Status Update Buttons -->
                                            @if(Auth::user()->role !== 'supplier' || Auth::user()->id === $shipment->supplier_id)
                                                @if($shipment->canBeShipped() && $shipment->status === 'pending')
                                                    <form action="{{ route('shipments.update-status', $shipment) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="shipped">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                                            title="Mark as Shipped">
                                                            <i class="bi bi-truck"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($shipment->canBeDelivered() && $shipment->status === 'shipped')
                                                    <form action="{{ route('shipments.update-status', $shipment) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="delivered">
                                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                            title="Mark as Delivered">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($shipment->canBeCancelled() && !in_array($shipment->status, ['delivered', 'cancelled']))
                                                    <form action="{{ route('shipments.update-status', $shipment) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Cancel Shipment"
                                                            onclick="return confirm('Are you sure you want to cancel this shipment?')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-truck text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3">No shipments found</h5>
                                            <p class="text-muted">
                                                {{ request()->has('search') ? 'Try a different search term or filter' : 'Create your first shipment to get started' }}
                                            </p>@if(Auth::user()->role !== 'supplier')
                                                <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-primary mt-2">
                                                    <i class="bi bi-plus-circle me-1"></i> Create Shipment
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($shipments->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $shipments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Export Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Shipments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('shipments.export') }}" method="GET">
                            <!-- Copy any active filters from the main form -->
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
    </div>
@endsection