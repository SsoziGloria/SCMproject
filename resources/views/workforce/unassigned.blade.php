@extends(auth()->user()->role . '.app')

@section('title', 'Available Workers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-primary fw-bold">Available Workers</h2>
        <p class="text-muted mb-0">These workers are ready for new assignments.</p>
    </div>
    <a href="{{ route('workforce.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Daily Roster
    </a>
</div>

@if($unassignedWorkers->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-people-fill fs-1 text-success"></i>
    <h5 class="mt-3">All Hands on Deck!</h5>
    <p class="text-muted">There are currently no workers available for assignment.</p>
</div>
@else
<div class="row">
    @foreach($unassignedWorkers as $worker)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start">
                    <i class="bi bi-person-circle fs-2 text-primary me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">{{ $worker->name }}</h5>
                        <p class="card-text text-muted">{{ $worker->position }}</p>
                    </div>
                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Available</span>
                </div>
                <div class="mt-auto pt-3 border-top">
                    <p class="card-text small mb-1"><i class="bi bi-envelope me-2"></i>{{ $worker->email }}</p>
                    <p class="card-text small mb-0"><i class="bi bi-telephone me-2"></i>{{ $worker->phone ?? 'No phone'
                        }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection