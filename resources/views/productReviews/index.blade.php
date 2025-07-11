@extends('retailer.app')

@section('content')
    <div class="container py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-star-half"></i> Product Reviews</h2>
        <a href="{{ route('productReviews.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Review
    </a>
</div>
        <div class="card shadow-sm border-primary">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $review->product->name ?? 'N/A' }}</td>
                                <td>{{ $review->reviewer_name }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                                </td>
                                <td>{{ $review->comment }}</td>
                                <td>{{ $review->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-5">
                                    <div class="alert alert-info mb-0 shadow-sm d-inline-block">
                                        <i class="bi bi-info-circle"></i> No product reviews found.
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