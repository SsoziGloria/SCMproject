@extends(auth()->user()->role . '.app')

@section('title', 'Worker Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">Workers</h2>
    <a href="{{ route('workers.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus-fill"></i> Add New Worker
    </a>
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
                        <th class="ps-4">Worker</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workers as $worker)
                    <tr class="align-middle">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-3 text-secondary"></i>
                                <div>
                                    <strong>{{ $worker->name }}</strong>
                                    <br><small class="text-muted">{{ $worker->position }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $worker->email }}
                            <br><small class="text-muted">{{ $worker->phone }}</small>
                        </td>
                        <td>
                            @if($worker->status == 'available')
                            <span class="badge bg-success-subtle text-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Available
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning rounded-pill">
                                <i class="bi bi-clock me-1"></i> Assigned
                            </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('workers.destroy', $worker) }}" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this worker? This cannot be undone.')">
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
                        <td colspan="4">
                            <div class="text-center py-5">
                                <i class="bi bi-people fs-1 text-primary"></i>
                                <h5 class="mt-3">No Workers Found</h5>
                                <p class="text-muted mb-3">Your worker roster is empty. Let's add your first team
                                    member.</p>
                                <a href="{{ route('workers.create') }}" class="btn btn-success">
                                    <i class="bi bi-person-plus-fill"></i> Add First Worker
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