@extends(auth()->user()->role . '.app')

@section('title', 'Edit Worker')
@section('content')
<div class="card mx-auto" style="max-width: 500px;">
    <div class="card-header bg-warning text-dark">Edit Worker</div>
    <div class="card-body">
        <form method="POST" action="{{ route('workers.update', $worker) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $worker->name) }}"
                    required>
                @error('name')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                    value="{{ old('email', $worker->email) }}" required>
                @error('email')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                    value="{{ old('phone', $worker->phone) }}">
                @error('phone')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" name="position" id="position" class="form-control"
                    value="{{ old('position', $worker->position) }}">
                @error('position')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Worker</button>
                <a href="{{ route('workers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif
    </div>
</div>
@endsection