@extends(auth()->user()->role . '.app')

@section('title', 'Create New Task')

@section('content')
<div class="card mx-auto" style="max-width: 600px;">
    <div class="card-header bg-success text-white">Create New Task</div>
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Task Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"
                    rows="3">{{ old('description') }}</textarea>
                @error('description')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="required_workers" class="form-label">Required Workers</label>
                        <input type="number" name="required_workers" id="required_workers" class="form-control"
                            value="{{ old('required_workers', 1) }}" min="1" required>
                        @error('required_workers')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority (1-10)</label>
                        <input type="number" name="priority" id="priority" class="form-control"
                            value="{{ old('priority', 5) }}" min="1" max="10" required>
                        @error('priority')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}"
                    required>
                @error('location')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{
                        old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">Active Task</label>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Create Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection