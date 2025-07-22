@extends(auth()->user()->role . '.app')

@section('content')
<div class="container mt-4">
    <h2>Search Results</h2>

    <div class="search-form mb-4">
        <form action="{{ route('search') }}" method="GET" class="d-flex">
            <input type="text" name="query" class="form-control me-2" value="{{ $query }}"
                placeholder="Search for products, orders, users...">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>

    @if($query)
    <p>Showing results for: <strong>{{ $query }}</strong></p>

    @if(count($results['products']) + count($results['orders']) + count($results['users']) == 0)
    <div class="alert alert-info">No results found for "{{ $query }}".</div>
    @endif

    @if(count($results['products']) > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h3>Products ({{ count($results['products']) }})</h3>
            @if(count($results['products']) == 10)
            <a href="{{ route('search.advanced', ['category' => 'products', 'query' => $query]) }}">View all</a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['products'] as $product)
                        <tr>
                            <td>{{ $product->product_id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>UGX {{ number_format($product->price, 0) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <a href="{{ route('products.show', $product->id) }}"
                                    class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(count($results['orders']) > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h3>Orders ({{ count($results['orders']) }})</h3>
            @if(count($results['orders']) == 10)
            <a href="{{ route('search.advanced', ['category' => 'orders', 'query' => $query]) }}">View all</a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['orders'] as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'processing' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>UGX {{ number_format($order->total_amount, 0) }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(count($results['users']) > 0 && auth()->user()->role === 'admin')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h3>Users ({{ count($results['users']) }})</h3>
            @if(count($results['users']) == 10)
            <a href="{{ route('search.advanced', ['category' => 'users', 'query' => $query]) }}">View all</a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['users'] as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @else
    <div class="text-center py-5">
        <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
        <p class="mt-3">Enter a search term to find products, orders, or users.</p>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        border-radius: 0.5rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #edf2f7;
        padding: 1rem 1.5rem;
    }

    .table th {
        font-weight: 600;
        color: #495057;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>
@endsection