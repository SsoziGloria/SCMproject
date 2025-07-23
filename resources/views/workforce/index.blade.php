@extends(auth()->user()->role . '.app')

@section('title', 'Daily Workforce Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">Daily Assignments</h2>
    <div>
        <a href="{{ route('workforce.history') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history"></i> View History
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Daily Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h4 class="text-primary">{{ $assignments->count() }}</h4>
                <small class="text-muted">Total Assignments Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h4 class="text-warning">{{ $assignments->where('status', 'assigned')->count() }}</h4>
                <small class="text-muted">Active Assignments</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h4 class="text-success">{{ $assignments->where('status', 'completed')->count() }}</h4>
                <small class="text-muted">Completed Tasks</small>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-task me-2"></i> Today's Roster ({{ now()->format('F j, Y') }})</h5>
        <a href="{{ route('workforce.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Worker</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr class="align-middle {{ $assignment->status == 'completed' ? 'opacity-75' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-2"></i>
                                <div>
                                    <strong>{{ $assignment->worker->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $assignment->worker->position ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $assignment->task }}</td>
                        <td><i class="bi bi-geo-alt me-1"></i> {{ $assignment->location }}</td>
                        <td>
                            @if($assignment->status == 'completed')
                            <span class="badge bg-success-subtle text-success rounded-pill">✓
                                Completed</span>
                            @else
                            <span class="badge bg-warning-subtle text-warning rounded-pill">Active</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($assignment->status == 'assigned')
                            <form method="POST" action="{{ route('workforce.finish', $assignment) }}" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-lg"></i> Finish Task
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">
                                @if($assignment->completed_at)
                                <i class="bi bi-clock"></i> {{ $assignment->completed_at }}
                                @endif
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-moon-stars fs-1 text-primary"></i>
                            <p class="text-muted mt-2 mb-1">It's quiet in here...</p>
                            <h5 class="mb-3">No assignments for today yet.</h5>
                            <a href="{{ route('tasks.auto-assign') }}" class="btn btn-primary">
                                <i class="bi bi-magic"></i> Start Auto-Assignment
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection