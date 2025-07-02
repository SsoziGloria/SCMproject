@extends('retailer.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-tags"></i> Categories</h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Category</a>
    </div>
    <div class="card shadow-sm border-primary">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description ?? '-' }}</td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-outline-info">View</a><br>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning">Edit</a><br>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
            
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center p-5">
                            <div class="alert alert-info mb-0 shadow-sm d-inline-block">
                                <i class="bi bi-info-circle"></i> No categories found.
                            
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