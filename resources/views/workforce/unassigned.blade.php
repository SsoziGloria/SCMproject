@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container mt-4">
        <h2 class="h4 fw-bold mb-4">Unassigned Workers</h2>

        @if($unassignedWorkers->isEmpty())
            <p class="text-muted">All workers have been assigned at least once.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unassignedWorkers as $worker)
                            <tr>
                                <td>{{ $worker->name }}</td>
                                <td>{{ $worker->email }}</td>
                                <td>{{ $worker->phone }}</td>
                                <td>{{ $worker->position }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection