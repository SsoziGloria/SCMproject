@extends('retailer.app')

@section('content')
    <div class="container py-4">
        <h2>Add Product Review</h2><br>
        <form action="{{ route('productReviews.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" class="form-select" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="reviewer_name" class="form-label">Reviewer Name</label>
                <input type="text" name="reviewer_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select name="rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea name="comment" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
            <a href="{{ route('productReviews.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection