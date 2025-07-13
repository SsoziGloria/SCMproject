@extends('layout')
@section('content')
<h2>Workforce Assignments</h2>
<div style="margin-bottom: 20px;">
    <a href="{{ route('workforce.create') }}" class="btn btn-primary" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Assign Worker</a>
    <a href="{{ route('workers.index') }}" class="btn btn-secondary" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Manage Workers</a>
</div>

<table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th>Worker Name</th>
            <th>Location</th>
            <th>Task</th>
            <th>Assigned Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assignments as $assignment)
            <tr>
                <td>{{ $assignment->worker->name ?? 'N/A' }}</td>
                <td>{{ $assignment->location }}</td>
                <td>{{ $assignment->task }}</td>
                <td>{{ $assignment->assigned_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@endsection 
