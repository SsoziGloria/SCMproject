@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Title & Actions -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="h3 mb-0 text-gray-800">{{ $product->name }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->product_id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'supplier' && auth()->user()->id === $product->supplier_id))
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil-square"></i> Edit Product
                    </a>
                @endif
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Product Image Column -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="img-fluid rounded mb-3" style="max-height: 300px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                style="height: 300px;">
                                <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                            </div>
                        @endif

                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }} me-2">
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </span>

                            @if($product->featured)
                                <span class="badge bg-primary ms-2">
                                    <i class="bi bi-star-fill"></i> Featured
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Column -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h5 class="card-title mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Basic Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-nowrap pe-4">Product Code</td>
                                        <td>{{ $product->product_id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Category</td>
                                        <td>{{ $product->category ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Price</td>
                                        <td>UGX {{ number_format($product->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Current Stock</td>
                                        <td>
                                            <span class="{{ $product->stock <= 5 ? 'text-danger' : '' }}">
                                                {{ $product->stock }} units
                                            </span>
                                            @if($product->stock <= 5 && $product->stock > 0)
                                                <span class="badge bg-warning text-dark ms-2">Low Stock</span>
                                            @elseif($product->stock <= 0)
                                                <span class="badge bg-danger ms-2">Out of Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted">Additional Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold text-nowrap pe-4">Supplier</td>
                                        <td>
                                            @if($product->supplier)
                                                {{-- <a href="{{ route('suppliers.show', $product->supplier->id) }}"> --}}
                                                    {{ $product->supplier->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Created</td>
                                        <td>{{ $product->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Last Updated</td>
                                        <td>{{ $product->updated_at->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status</td>
                                        <td>
                                            <span class="badge {{ $product->featured ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $product->featured ? 'Featured' : 'Standard' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($product->description)
                            <div class="mb-4">
                                <h6 class="text-muted">Description</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $product->description }}
                                </div>
                            </div>
                        @endif

                        @if($product->ingredients)
                            <div>
                                <h6 class="text-muted">Ingredients</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $product->ingredients }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if(auth()->user()->role === 'admin')
                    <div class="card shadow-sm">
                        <div class="card-header py-3">
                            <h5 class="card-title mb-0">Admin Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit Product
                                </a>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#updateStockModal">
                                    <i class="bi bi-plus-circle"></i> Update Stock
                                </button>

                                <form action="{{ route('products.toggle-featured', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-star{{ $product->featured ? '-fill' : '' }}"></i>
                                        {{ $product->featured ? 'Remove from Featured' : 'Mark as Featured' }}
                                    </button>
                                </form>

                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal">
                                    <i class="bi bi-trash"></i> Delete Product
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Stock Modal -->
    @if(auth()->user()->role === 'admin')
        <div class="modal fade" id="updateStockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('products.update-stock', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Current Stock: {{ $product->stock }}</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0"
                                    value="{{ $product->stock }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Stock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Product Modal -->
        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection