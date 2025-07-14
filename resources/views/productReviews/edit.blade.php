@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Edit Review</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productReviews.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Reviews
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('productReviews.update', $review->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id"
                            name="product_id" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ (old('product_id', $review->product_id) == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->product_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reviewer_name" class="form-label">Reviewer Name</label>
                        <input type="text" class="form-control @error('reviewer_name') is-invalid @enderror"
                            id="reviewer_name" name="reviewer_name"
                            value="{{ old('reviewer_name', $review->reviewer_name) }}" required>
                        @error('reviewer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <div class="rating-input">
                            <div class="d-flex align-items-center">
                                <div class="star-rating">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" {{ (old('rating', $review->rating) == $i) ? 'checked' : '' }} required>
                                        <label for="rating{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                    @endfor
                                </div>
                                <span class="ms-3" id="ratingText">
                                    @php
                                        $ratingTexts = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                                        $currentRating = old('rating', $review->rating);
                                        echo $currentRating ? $ratingTexts[$currentRating - 1] : 'Select Rating';
                                    @endphp
                                </span>
                            </div>
                        </div>
                        @error('rating')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment"
                            rows="4">{{ old('comment', $review->comment) }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Submitted</label>
                        <p class="form-control-static">{{ $review->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            font-size: 1.5rem;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            color: #ccc;
            margin-right: 5px;
        }

        .star-rating :checked~label {
            color: #ffb400;
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffb400;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const ratingText = document.getElementById('ratingText');
            const ratingTexts = [
                'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'
            ];

            ratingInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const value = parseInt(this.value);
                    ratingText.textContent = ratingTexts[value - 1];
                });
            });
        });
    </script>
@endpush