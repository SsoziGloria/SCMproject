@extends(auth()->user()->role . '.app')

@section('title', 'Manual Task Assignment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">✍️ Manual Assignment</h2>
    <a href="{{ route('workforce.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Daily Roster
    </a>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 700px;">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Assign a Worker to a Task</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('workforce.store') }}">
            @csrf

            <div class="mb-3">
                <label for="worker_id" class="form-label"><i class="bi bi-person-check"></i> Available Worker</label>
                <select name="worker_id" id="worker_id" class="form-select" required>
                    <option value="" disabled selected>Select a worker...</option>
                    @forelse($availableWorkers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->position }})</option>
                    @empty
                    <option value="" disabled>No workers are currently available.</option>
                    @endforelse
                </select>
                @error('worker_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="task_name" class="form-label"><i class="bi bi-card-checklist"></i> Task</label>
                <select name="task" id="task_name" class="form-select" required>
                    <option value="" disabled selected>Select a task...</option>
                    @foreach($activeTasks as $task)
                    <option value="{{ $task->name }}" data-location="{{ $task->location }}">{{ $task->name }}</option>
                    @endforeach
                </select>
                @error('task') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="location" class="form-label"><i class="bi bi-geo-alt"></i> Location</label>
                <input type="text" name="location" id="location" class="form-control bg-light"
                    placeholder="Location will be auto-filled" readonly required>
                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="assigned_date" class="form-label"><i class="bi bi-calendar-event"></i> Assignment
                    Date</label>
                <input type="date" name="assigned_date" id="assigned_date" class="form-control"
                    value="{{ now()->format('Y-m-d') }}" required>
                @error('assigned_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end pt-3 border-top">
                <a href="{{ route('workforce.index') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Assign Worker
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const taskSelect = document.getElementById('task_name');
    const locationInput = document.getElementById('location');

    taskSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const location = selectedOption.getAttribute('data-location');
        locationInput.value = location ? location : '';
    });
});
</script>
@endpush