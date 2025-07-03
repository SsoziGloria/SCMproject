{{-- filepath: resources/views/cart/index.blade.php --}}
@extends('user.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Shopping Cart</h2>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                @if(count($products) > 0)
                    <form action="{{ route('cart.update') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th width="15%">Quantity</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $id => $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item['product']->image)
                                                            <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                                alt="{{ $item['product']->name }}" class="img-thumbnail me-3"
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light me-3" style="width: 60px; height: 60px;"></div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $item['product']->name }}</h6>
                                                            @if($item['product']->category)
                                                                <small class="text-muted">{{ $item['product']->category }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>UGX {{ number_format($item['product']->price, 0) }}</td>
                                                <td>
                                                    <input type="number" name="quantities[{{ $id }}]"
                                                        value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}"
                                                        class="form-control form-control-sm">
                                                </td>
                                                <td>UGX {{ number_format($item['product']->price * $item['quantity'], 0) }}</td>
                                                <td>
                                                    <a href="{{ route('cart.remove', $id) }}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to remove this item?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-outline-primary">
                                    Update Cart
                                </button>
                                <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger ms-2"
                                    onclick="return confirm('Are you sure you want to clear your cart?')">
                                    Clear Cart
                                </a>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <i class="bi bi-cart" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Your cart is empty</h4>
                            <p class="text-muted">Add some products to your cart and they will appear here</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary mt-3">Browse Products</a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>UGX {{ number_format($total, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Transport:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong>UGX {{ number_format($total, 0) }}</strong>
                        </div>

                        @if(count($products) > 0)
                            <a href="{{ route('cart.checkout') }}" class="btn btn-success w-100">Proceed to Checkout</a>
                        @else
                            <button class="btn btn-success w-100" disabled>Proceed to Checkout</button>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6>We Accept</h6>
                        <div class="d-flex gap-2">
                            <img src="{{ asset('assets/img/mtn.svg')}}" style="width: 40px;"></i>
                            <img src="{{ asset('assets/img/airtel.svg')}}" style="width: 20px;"></i>
                            <i class="bi bi-credit-card-2-front-fill" style="font-size: 1.5rem;"></i>
                            <i class="bi bi-bank" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection