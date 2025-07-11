@extends('retailer.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-graph-up"></i> Stock Levels</h2>
    <div class="card shadow-sm border-primary">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th> 
                        <th>Product</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
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
                        <td>
                            <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-5">
                            <div class="alert alert-info mb-0 shadow-sm d-inline-block">
                                <i class="bi bi-info-circle"></i> No stock data found.
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