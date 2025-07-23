@extends(auth()->user()->role . '.app')

@section('title', 'Task Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">🎯 Task Management</h2>
    <div>
        <a href="{{ route('tasks.auto-assign') }}" class="btn btn-primary me-2">
            <i class="bi bi-magic"></i> Run Auto-Assign
        </a>
        <a href="{{ route('tasks.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Add New Task
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Task</th>
                        <th>Details</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr class="align-middle">
                        <td class="ps-4">
                            <strong>{{ $task->name }}</strong>
                            @if($task->description)
                            <br><small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="d-block mb-1"><i class="bi bi-geo-alt text-muted me-2"></i>{{ $task->location
                                }}</span>
                            <span class="d-block"><i class="bi bi-people text-muted me-2"></i>{{ $task->required_workers
                                }} Workers</span>
                        </td>
                        <td>
                            @php
                            $priorityClass = '';
                            if ($task->priority >= 9) $priorityClass = 'danger';
                            elseif ($task->priority >= 7) $priorityClass = 'warning';
                            elseif ($task->priority >= 5) $priorityClass = 'info';
                            else $priorityClass = 'secondary';
                            @endphp
                            <span
                                class="badge bg-{{ $priorityClass }}-subtle text-{{ $priorityClass }} border border-{{ $priorityClass }}-subtle rounded-pill fs-6">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td>
                            @if($task->is_active)
                            <span class="badge bg-success-subtle text-success rounded-pill mb-1 d-inline-block">
                                <i class="bi bi-power"></i> Active
                            </span>
                            @if($task->status_for_day == 'pending')
                            <span class="badge bg-primary-subtle text-primary rounded-pill d-inline-block">
                                <i class="bi bi-person-plus"></i> Needs Staff
                            </span>
                            @else
                            <span class="badge bg-info-subtle text-info rounded-pill d-inline-block">
                                <i class="bi bi-people-fill"></i> Staffed
                            </span>
                            @endif
                            @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                <i class="bi bi-archive"></i> Inactive
                            </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <i class="bi bi-journal-check fs-1 text-primary"></i>
                                <h5 class="mt-3">No Tasks Created Yet</h5>
                                <p class="text-muted mb-3">Get started by creating your first task for the workforce.
                                </p>
                                <a href="{{ route('tasks.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle-fill"></i> Create First Task
                                </a>
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