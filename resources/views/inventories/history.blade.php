@extends('supplier.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inventory History</h5>
                <div>
                    <a href="{{ route('inventories.create') }}" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-plus"></i> Add New
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search & Filters -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ route('inventories.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search product name, batch, location..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end">
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <div class="dropdown-menu p-3" style="width: 250px;" aria-labelledby="filterDropdown">
                                <form action="{{ route('inventories.index') }}" method="GET">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">All Statuses</option>
                                            <option value="available" {{ request('status')=='available' ? 'selected'
                                                : '' }}>Available</option>
                                            <option value="reserved" {{ request('status')=='reserved' ? 'selected' : ''
                                                }}>Reserved</option>
                                            <option value="damaged" {{ request('status')=='damaged' ? 'selected' : ''
                                                }}>Damaged</option>
                                            <option value="expired" {{ request('status')=='expired' ? 'selected' : ''
                                                }}>Expired</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Expiration</label>
                                        <select name="expiration" class="form-select form-select-sm">
                                            <option value="">All</option>
                                            <option value="soon" {{ request('expiration')=='soon' ? 'selected' : '' }}>
                                                Expiring Soon</option>
                                            <option value="expired" {{ request('expiration')=='expired' ? 'selected'
                                                : '' }}>Expired</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Supplier</label>
                                        <select name="supplier_id" class="form-select form-select-sm">
                                            <option value="">All Suppliers</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}" {{
                                                request('supplier_id')==$supplier->supplier_id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-sm btn-primary">Apply Filters</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-sort-down"></i> Sort
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item {{ request('sort') == 'newest' ? 'active' : '' }}"
                                        href="{{ route('inventories.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}">
                                        Newest First
                                    </a>
                                </li>
                                <li><a class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}"
                                        href="{{ route('inventories.index', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}">
                                        Oldest First
                                    </a>
                                </li>
                                <li><a class="dropdown-item {{ request('sort') == 'quantity_asc' ? 'active' : '' }}"
                                        href="{{ route('inventories.index', array_merge(request()->except('sort'), ['sort' => 'quantity_asc'])) }}">
                                        Quantity (Low to High)
                                    </a>
                                </li>
                                <li><a class="dropdown-item {{ request('sort') == 'quantity_desc' ? 'active' : '' }}"
                                        href="{{ route('inventories.index', array_merge(request()->except('sort'), ['sort' => 'quantity_desc'])) }}">
                                        Quantity (High to Low)
                                    </a>
                                </li>
                                <li><a class="dropdown-item {{ request('sort') == 'expiry' ? 'active' : '' }}"
                                        href="{{ route('inventories.index', array_merge(request()->except('sort'), ['sort' => 'expiry'])) }}">
                                        Expiration Date
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['total_items'] }}</h5>
                            <p class="card-text">Total Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['product_count'] }}</h5>
                            <p class="card-text">Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['low_stock_count'] }}</h5>
                            <p class="card-text">Low Stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['expiring_soon_count'] }}</h5>
                            <p class="card-text">Expiring Soon</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Table -->
            @if($inventory->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Expiration</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product_name }}" class="rounded me-2" width="40">
                                    @else
                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-box"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->product)
                                        <div class="small text-muted">ID: {{ $item->product_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold {{ $item->quantity < 10 ? 'text-danger' : '' }}">
                                    {{ $item->quantity }}
                                </span>
                                <span class="text-muted">{{ $item->unit }}</span>
                            </td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->batch_number ?: 'N/A' }}</td>
                            <td>
                                @php
                                $statusClass = [
                                'available' => 'success',
                                'reserved' => 'primary',
                                'damaged' => 'danger',
                                'expired' => 'dark'
                                ][$item->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                @if($item->expiration_date)
                                @php
                                $today = \Carbon\Carbon::now();
                                $expiryDate = \Carbon\Carbon::parse($item->expiration_date);
                                $daysLeft = $today->diffInDays($expiryDate, false);
                                @endphp

                                @if($daysLeft < 0) <span class="badge bg-danger">Expired</span>
                                    @elseif($daysLeft <= 30) <span class="badge bg-warning text-dark">{{ $daysLeft }}
                                        days left</span>
                                        @else
                                        {{ $item->expiration_date->format('M d, Y') }}
                                        @endif
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                            </td>
                            <td>
                                {{ $item->supplier->name ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('inventories.edit', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $item->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Delete Modal for each inventory item -->
                        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this inventory record for
                                            <strong>{{ $item->product_name }}</strong>?
                                        </p>
                                        <p class="text-danger"><small>This action cannot be undone.</small></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('inventories.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete Record</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $inventory->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="mt-3 mb-0">No inventory records found.</p>
                @if(request()->has('search') || request()->has('status') || request()->has('supplier_id') ||
                request()->has('expiration'))
                <p class="text-muted">Try adjusting your filters.</p>
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-primary mt-2">Clear Filters</a>
                @else
                <a href="{{ route('inventories.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus"></i> Add Inventory
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection