@extends('layout')
@section('content')
<h2>Workers Management</h2>

<div class="mb-3">
    <a href="{{ route('workers.create') }}" class="btn btn-success">Add New Worker</a>
</div>

@if(session('success'))
    <div class="alert alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if($workers->count() > 0)
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Assignment Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workers as $worker)
                <tr>
                    <td>{{ $worker->id ?? 'N/A' }}</td>
                    <td>{{ $worker->name ?? 'N/A' }}</td>
                    <td>{{ $worker->email ?? 'N/A' }}</td>
                    <td>{{ $worker->phone ?? 'N/A' }}</td>
                    <td>{{ $worker->position ?? 'N/A' }}</td>
                    <td>
                        @if($worker->assignments && $worker->assignments->count() > 0)
                            <span class="badge bg-success">Assigned</span>
                        @else
                            <span class="badge bg-secondary">Not Assigned</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form method="POST" action="{{ route('workers.destroy', $worker) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this worker?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-center text-muted mt-4">No workers found. <a href="{{ route('workers.create') }}">Add your first worker</a></p>
@endif

<div class="mt-4">
    <a href="{{ route('workforce.index') }}" class="btn btn-primary">Back to Workforce</a>
</div>
@endsection 