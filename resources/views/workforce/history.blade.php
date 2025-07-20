@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container mt-4">
        <h2 class="h4 fw-bold mb-4">Assignment History</h2>

        @if($history->isEmpty())
            <p class="text-muted">No historical assignments found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Worker</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Task</th>
                            <th>Location</th>
                            <th>Assigned Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $assignment)
                            <tr>
                                <td>{{ $assignment->worker->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->worker->email ?? 'N/A' }}</td>
                                <td>{{ $assignment->worker->phone ?? 'N/A' }}</td>
                                <td>{{ ucfirst($assignment->task) }}</td>
                                <td>{{ $assignment->location }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->assigned_date)->toFormattedDateString() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection