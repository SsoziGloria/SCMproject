{{-- filepath: resources/views/shop/product.blade.php --}}
@extends('user.app')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}"
                            style="max-height: 400px; object-fit: contain;">
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px;">
                            <span class="text-muted">No Image Available</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
                        @if($product->category)
                            <li class="breadcrumb-item"><a
                                    href="{{ route('shop.index', ['category' => $product->category]) }}">{{ $product->category }}</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>

                <h1 class="mb-3">{{ $product->name }}</h1>

                <!-- Product Code -->
                <p class="text-muted mb-3">Product Code: {{ $product->product_id }}</p>

                <!-- Price -->
                <h2 class="text-success mb-4">UGX {{ number_format($product->price, 0) }}</h2>

                <!-- Availability -->
                <p class="mb-3">
                    <strong>Availability:</strong>
                    <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                        {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </p>

                <!-- Description -->
                @if($product->description)
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p>{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Ingredients -->
                @if($product->ingredients)
                    <div class="mb-4">
                        <h5>Ingredients</h5>
                        <p>{{ $product->ingredients }}</p>
                    </div>
                @endif

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="row g-3">
                            <div class="col-auto">
                                <div class="input-group" style="width: 130px;">
                                    <span class="input-group-text">Qty</span>
                                    <input type="number" class="form-control" name="quantity" value="1" min="1"
                                        max="{{ $product->stock }}">
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </div>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary mb-4" disabled>Out of Stock</button>
                @endif

                <!-- Supplier Info (if available) -->
                @if(isset($product->supplier) && $product->supplier)
                    <div class="mt-4 p-3 bg-light rounded">
                        <h5>Supplied by: {{ $product->supplier->name }}</h5>
                        @if(isset($product->supplier->certification_status) && $product->supplier->certification_status)
                            <p class="mb-0"><span class="badge bg-info">{{ $product->supplier->certification_status }}</span></p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Reviews -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="border-bottom pb-2 mb-4">Customer Reviews</h3>

                @if(isset($reviews) && $reviews->count() > 0)
                    <div class="mb-4">
                        @foreach($reviews as $review)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">{{ $review->reviewer_name }}</h5>
                                        <div>
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="card-text">{{ $review->comment }}</p>
                                    <p class="card-text"><small class="text-muted">Posted on
                                            {{ $review->created_at->format('M d, Y') }}</small></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                @endif

                <!-- Review Form -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Write a Review</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shop.product.review', $product->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reviewer_name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="reviewer_name" name="reviewer_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating1" value="1"
                                            required>
                                        <label class="form-check-label" for="rating1">1</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                        <label class="form-check-label" for="rating2">2</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                                        <label class="form-check-label" for="rating3">3</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                        <label class="form-check-label" for="rating4">4</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                        <label class="form-check-label" for="rating5">5</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Review</label>
                                <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="border-bottom pb-2 mb-4">You May Also Like</h3>
                </div>
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            @if($relatedProduct->image)
                                <img src="{{ asset('storage/' . $relatedProduct->image) }}" class="card-img-top"
                                    alt="{{ $relatedProduct->name }}" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-light text-center py-4">No Image</div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <p class="text-success mb-0">UGX {{ number_format($relatedProduct->price, 0) }}</p>
                                    <span class="badge bg-{{ $relatedProduct->stock > 0 ? 'success' : 'danger' }} small">
                                        {{ $relatedProduct->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                                <a href="{{ route('shop.product', $relatedProduct->id) }}"
                                    class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection