@extends('user.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="bg-light rounded-3 p-5 text-center">
                <h1 class="display-5 fw-bold">Premium Chocolate Shop</h1>
                <p class="fs-4">Handcrafted chocolates sourced through our sustainable supply chain</p>
                <form action="{{ route('shop.index') }}" method="GET" class="d-flex justify-content-center mt-4">
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" name="search" class="form-control" placeholder="Search products..."
                            value="{{ request('search') }}">
                        <select name="category" class="form-select" style="max-width: 150px;">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category')==$category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    @if(isset($featured) && $featured->count() > 0 && !request('search') && !request('category'))
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="border-bottom pb-2 mb-4">Featured Products</h2>
        </div>
        @foreach($featured as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}"
                    style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-light text-center py-5">No Image</div>
                @endif
                <div class="card-body d-flex flex-column py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <span class="badge bg-warning">Featured</span>
                    </div>
                    <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 80) }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <h5 class="text-success mb-0">UGX {{ number_format($product->price, 0) }}</h5>
                        <span class="badge bg-{{ $product->available_stock > 0 ? 'success' : 'danger' }}">
                            {{ $product->available_stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('shop.product', $product->id) }}" class="btn btn-outline-primary btn-sm">View
                            Details</a>
                        @if($product->available_stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add to Cart</button>
                        </form>
                        @else
                        <button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- All Products / Search Results -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h2>{{ request('search') || request('category') ? 'Search Results' : 'All Products' }}</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Sort By
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">Price: Low to High</a>
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">Price: High to
                                Low</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">Name: A to Z</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">Name: Z to A</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if($products->count() > 0)
        @foreach($products as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}"
                    style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-light text-center py-5">No Image</div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    @if($product->category)
                    <p class="card-text"><span class="badge bg-secondary">{{ $product->category }}</span></p>
                    @endif
                    <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <h5 class="text-success mb-0">UGX {{ number_format($product->price, 0) }}</h5>
                        <span class="badge bg-{{ $product->available_stock > 0 ? 'success' : 'danger' }}">
                            {{ $product->available_stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('shop.product', $product->id) }}" class="btn btn-outline-primary btn-sm">View
                            Details</a>
                        @if($product->stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add to Cart</button>
                        </form>
                        @else
                        <button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div class="col-12">
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-search" style="font-size: 3rem;"></i>
                <p class="mt-3">No products found matching your criteria.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary mt-2">View All Products</a>
            </div>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection