@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Product Reviews</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productReviews.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Add New Review
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Reviewer</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td>
                                        <a href="{{ route('products.show', $review->product->id) }}">
                                            {{ $review->product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $review->reviewer_name }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                            @endfor
                                            <span class="ms-2">{{ $review->rating }}/5</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;">
                                            {{ $review->comment ?? 'No comment' }}
                                        </div>
                                    </td>
                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('productReviews.edit', $review->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $review->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this review from
                                                    {{ $review->reviewer_name }}?
                                                </p>
                                                <div class="bg-light p-3 rounded">
                                                    <div class="d-flex mb-2">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="mb-0">{{ $review->comment ?? 'No comment' }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('productReviews.destroy', $review->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-chat-square-text" style="font-size: 2rem; color: #ddd;"></i>
                                            <p class="mt-2 mb-0 text-muted">No product reviews found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection