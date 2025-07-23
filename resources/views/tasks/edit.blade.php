@extends(auth()->user()->role . '.app')

@section('title', 'Edit Task')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">✏️ Edit Task</h2>
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tasks
    </a>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 700px;">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="bi bi-pencil-square"></i> Edit Task: {{ $task->name }}
        </h5>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="updateTaskForm" method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="bi bi-tag"></i> Task Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $task->name) }}"
                    required>
                @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">
                    <i class="bi bi-file-text"></i> Description
                </label>
                <textarea name="description" id="description" class="form-control" rows="4"
                    placeholder="Enter task description...">{{ old('description', $task->description) }}</textarea>
                @error('description')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="required_workers" class="form-label">
                            <i class="bi bi-people"></i> Required Workers <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="required_workers" id="required_workers" class="form-control"
                            value="{{ old('required_workers', $task->required_workers) }}" min="1" max="50" required>
                        <div class="form-text">Number of workers needed for this task</div>
                        @error('required_workers')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="priority" class="form-label">
                            <i class="bi bi-exclamation-triangle"></i> Priority <span class="text-danger">*</span>
                        </label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="">Select Priority</option>
                            @for($i = 1; $i <= 10; $i++) <option value="{{ $i }}" {{ old('priority', $task->priority) ==
                                $i ? 'selected' : '' }}>
                                {{ $i }} -
                                @if($i >= 9) Critical
                                @elseif($i >= 7) High
                                @elseif($i >= 5) Medium
                                @elseif($i >= 3) Low
                                @else Very Low
                                @endif
                                </option>
                                @endfor
                        </select>
                        <div class="form-text">Higher numbers = higher priority</div>
                        @error('priority')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">
                    <i class="bi bi-geo-alt"></i> Location <span class="text-danger">*</span>
                </label>
                <input type="text" name="location" id="location" class="form-control"
                    value="{{ old('location', $task->location) }}" placeholder="e.g., Factory A, Warehouse B, QC Lab"
                    required>
                @error('location')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{
                        old('is_active', $task->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">
                        <i class="bi bi-toggle-on"></i> Active Task
                    </label>
                    <div class="form-text">Only active tasks will be included in auto-assignment</div>
                </div>
            </div>

            <!-- Current Assignment Info -->
            @php
            $todayAssignments = \App\Models\Workforces::where('task', $task->name)
            ->whereDate('assigned_date', today())
            ->with('worker')
            ->get();
            @endphp

            @if($todayAssignments->count() > 0)
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle"></i> Today's Assignments for this Task:</h6>
                <ul class="mb-0">
                    @foreach($todayAssignments as $assignment)
                    <li>{{ $assignment->worker->name }} ({{ $assignment->location }})</li>
                    @endforeach
                </ul>
                <small class="text-muted">
                    {{ $todayAssignments->count() }} of {{ $task->required_workers }} workers assigned
                </small>
            </div>
            @endif
        </form> <!-- CLOSE the update form here, before the buttons -->

        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <!-- Left side: Update and Cancel buttons -->
            <div>
                <button type="submit" form="updateTaskForm" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Update Task
                </button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>

            <!-- Right side: Delete button in its own separate form -->
            <div>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="mb-0"
                    onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Task
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>


<!-- Task Statistics Card -->
<div class="card mt-4 mx-auto" style="max-width: 700px;">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-graph-up"></i> Task Statistics</h6>
    </div>
    <div class="card-body">
        <div class="row text-center">
            @php
            $totalAssignments = \App\Models\Workforces::where('task', $task->name)->count();
            $thisWeekAssignments = \App\Models\Workforces::where('task', $task->name)
            ->whereBetween('assigned_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
            $thisMonthAssignments = \App\Models\Workforces::where('task', $task->name)
            ->whereMonth('assigned_date', now()->month)
            ->count();
            @endphp

            <div class="col-md-3">
                <div class="border-end">
                    <h4 class="text-primary">{{ $totalAssignments }}</h4>
                    <small class="text-muted">Total Assignments</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border-end">
                    <h4 class="text-success">{{ $thisWeekAssignments }}</h4>
                    <small class="text-muted">This Week</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border-end">
                    <h4 class="text-info">{{ $thisMonthAssignments }}</h4>
                    <small class="text-muted">This Month</small>
                </div>
            </div>
            <div class="col-md-3">
                <h4 class="text-warning">{{ $task->required_workers }}</h4>
                <small class="text-muted">Required Workers</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Priority color coding
    const prioritySelect = document.getElementById('priority');
    
    function updatePriorityColor() {
        const value = parseInt(prioritySelect.value);
        prioritySelect.className = 'form-select';
        
        if (value >= 9) {
            prioritySelect.classList.add('border-danger');
        } else if (value >= 7) {
            prioritySelect.classList.add('border-warning');
        } else if (value >= 5) {
            prioritySelect.classList.add('border-info');
        } else {
            prioritySelect.classList.add('border-secondary');
        }
    }
    
    prioritySelect.addEventListener('change', updatePriorityColor);
    updatePriorityColor(); // Initial call
    
    // Worker count validation
    const workersInput = document.getElementById('required_workers');
    workersInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (value > 50) {
            this.setCustomValidity('Maximum 50 workers allowed per task');
        } else if (value < 1) {
            this.setCustomValidity('At least 1 worker is required');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush