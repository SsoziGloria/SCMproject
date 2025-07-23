@extends(auth()->user()->role . '.app')

@section('title', 'Assignment History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">Assignment History</h2>
    <a href="{{ route('workforce.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Daily Roster
    </a>
</div>

@if($history->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-archive fs-1 text-primary"></i>
    <h5 class="mt-3">No Historical Data Found</h5>
    <p class="text-muted">Once tasks are completed, their history will appear here.</p>
</div>
@else
@foreach($history as $date => $assignments)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0 fw-bold">{{ $date }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Worker</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr class="align-middle">
                        <td class="ps-4">
                            <strong>{{ $assignment->worker->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $assignment->worker->position ?? '' }}</small>
                        </td>
                        <td>
                            {{ $assignment->task }}
                            <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $assignment->location
                                }}</small>
                        </td>
                        <td>
                            @if($assignment->status == 'completed')
                            <span class="badge bg-success-subtle text-success rounded-pill">✓ Completed</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Assigned</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($assignment->completed_at)
                            <span class="text-muted">{{ $assignment->completed_at }}</span>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection