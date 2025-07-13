@extends('layout')
@section('title', 'Assign Worker to Task')
@section('content')
<div class="card mx-auto" style="max-width: 600px;">
    <div class="card-header bg-primary text-white">Assign Worker to Task</div>
    <div class="card-body">
        <form method="POST" action="{{ route('workforce.store') }}">
            @csrf
            <div class="mb-3">
                <label for="worker_id" class="form-label">Worker</label>
                <select name="worker_id" id="worker_id" class="form-select" required>
                    <option value="">Select a worker</option>
                    @if(isset($workers) && $workers->count() > 0)
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->position }})</option>
                        @endforeach
                    @else
                        <option value="">No workers available</option>
                    @endif
                </select>
                @error('worker_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" required>
                @error('location')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="task" class="form-label">Task</label>
                <input type="text" name="task" id="task" class="form-control" required>
                @error('task')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="assigned_date" class="form-label">Start Date</label>
                <input type="date" name="assigned_date" id="assigned_date" class="form-control" required>
                @error('assigned_date')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Assign</button>
                <a href="{{ route('workforce.index') }}" class="btn btn-secondary">Back to Workforce</a>
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