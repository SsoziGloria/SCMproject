@extends('layout')
@section('title', 'Dashboard')
@section('content')
<div class="mb-4">
    <h2 class="fw-bold">{{ $greeting }}, {{ $user->name }}!</h2>
    <p class="text-muted">Welcome to your Workforce Management Dashboard.</p>
</div>
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Total Workers</h5>
                <h2 class="card-text">{{ $totalWorkers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Total Assignments</h5>
                <h2 class="card-text">{{ $totalAssignments }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Assigned Workers</h5>
                <h2 class="card-text">{{ $assignedWorkers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-secondary h-100">
            <div class="card-body">
                <h5 class="card-title">Unassigned Workers</h5>
                <h2 class="card-text">{{ $unassignedWorkers }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light fw-bold">Recent Assignments</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Worker</th>
                            <th>Task</th>
                            <th>Location</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->worker->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->task }}</td>
                                <td>{{ $assignment->location }}</td>
                                <td>{{ $assignment->assigned_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent assignments.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light fw-bold">Quick Links</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('workers.index') }}" class="btn btn-outline-primary">Manage Workers</a>
                <a href="{{ route('workforce.index') }}" class="btn btn-outline-success">View Assignments</a>
                <a href="{{ route('workforce.create') }}" class="btn btn-outline-info">Assign Worker</a>
                <a href="{{ route('logout') }}" class="btn btn-outline-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
