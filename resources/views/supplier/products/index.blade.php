@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>My Products</h1>
            <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Product
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Product Inventory</h5>
                    </div>
                    <div class="col-auto">
                        <form action="{{ route('supplier.products.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2"
                                placeholder="Search products" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                            class="img-thumbnail" style="height: 50px; width: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                            style="height: 50px; width: 50px;">
                                            <i class="bi bi-image text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $product->product_id }}</span></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                <td>UGX {{ number_format($product->price, 0) }}</td>
                                <td>
                                    @if($product->stock > 10)
                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                    @elseif($product->stock > 0)
                                        <span class="badge bg-warning text-dark">{{ $product->stock }}</span>
                                    @else
                                        <span class="badge bg-danger">Out of stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->featured)
                                        <span class="badge bg-info"><i class="bi bi-star-fill"></i> Featured</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('supplier.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $product->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete <strong>{{ $product->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('supplier.products.destroy', $product->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox text-secondary" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">No products found</p>
                                        <a href="{{ route('supplier.products.create') }}" class="btn btn-sm btn-primary mt-3">
                                            Add Your First Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection