@extends('retailer.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-box-seam"></i> All Products</h2>

            <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add
                Product</a>
            <a href="#" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Product</a>
        </div>
        <div class="card shadow-sm border-primary">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $product->stock < 10 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info"
                                        class="btn btn-sm btn-outline-info">View</a><br>

                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info"
                                        class="btn btn-sm btn-outline-info">View</a>

                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning"
                                        class="btn btn-sm btn-outline-warning">Edit</a><br>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-5">
                                    <div class="alert alert-info mb-0 shadow-sm d-inline-block">
                                        <i class="bi bi-info-circle"></i> No products found.
                                        <a href="{{ route('products.create') }}" class="btn btn-primary"><i
                                                class="bi bi-plus-circle" class="btn btn-sm btn-primary ms-2"><i
                                                    class="bi bi-plus-circle"></i> Add Product</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection