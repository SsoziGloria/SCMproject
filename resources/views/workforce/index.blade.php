@extends('layout')

@section('title', 'Assigned Tasks')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">📋 Assigned Tasks</h2>
        <div>
            <a href="{{ route('workforce.history') }}" class="btn btn-outline-secondary">History</a>
            <a href="{{ route('workforce.unassigned') }}" class="btn btn-outline-warning">Unassigned</a>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Worker</th>
                <th>Location</th>
                <th>Task</th>
                <th>Assigned Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->worker->name }}</td>
                    <td>{{ $assignment->location }}</td>
                    <td>{{ $assignment->task }}</td>
                    <td>{{ $assignment->assigned_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
