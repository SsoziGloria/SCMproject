@extends(auth()->user()->role . '.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Low Stock Items Requiring Reorder
                </h5>
                <div>
                    <a href="{{ route('inventories.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Inventory
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($reorders->count() > 0)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-circle"></i>
                You have <strong>{{ $reorders->count() }}</strong> items with low stock levels that require reordering.
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Current Quantity</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reorders as $item)
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
                                        <div class="small text-muted">
                                            Location: {{ $item->location ?: 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-danger fw-bold">{{ $item->quantity }}</span>
                                <span class="text-muted">{{ $item->unit }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            </td>
                            <td>
                                {{ $item->supplier->name ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $item->updated_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('inventories.edit', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                        data-bs-target="#reorderModal{{ $item->id }}">
                                        <i class="bi bi-cart-plus"></i> Reorder
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Reorder Modal -->
                        <div class="modal fade" id="reorderModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Reorder Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('inventories.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Product</label> <input type="text"
                                                    class="form-control" value="{{ $item->product_name }}" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="quantity{{ $item->id }}" class="form-label">Current
                                                    Quantity</label> <input type="number" id="quantity{{ $item->id }}"
                                                    class="form-control" value="{{ $item->quantity }}" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="reorder_quantity{{ $item->id }}" class="form-label">Reorder
                                                    Quantity</label>
                                                <input type="number" id="reorder_quantity{{ $item->id }}"
                                                    name="quantity" class="form-control" min="1" required>
                                                <div class="form-text">Enter the new total quantity after receiving
                                                    reordered items</div>
                                            </div>

                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <input type="hidden" name="product_name" value="{{ $item->product_name }}">
                                            <input type="hidden" name="location" value="{{ $item->location }}">
                                            <input type="hidden" name="supplier_id" value="{{ $item->supplier_id }}">
                                            <input type="hidden" name="expiration_date"
                                                value="{{ $item->expiration_date?->format('Y-m-d') }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Update Inventory</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-success"></i>
                <p class="mt-3 mb-0">All your inventory items have sufficient stock levels.</p>
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-primary mt-3">View All Inventory</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection