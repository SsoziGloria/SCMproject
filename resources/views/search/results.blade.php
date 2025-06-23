@extends(auth()->user()->role . '.app')

@section('content')
    <h2>Search Results for "{{ $query }}"</h2>
    @if($results->isEmpty())
        <p>No results found.</p>
    @else
        <ul>
            @foreach($results as $user)
                <li>{{ $user->name }} ({{ $user->email }})</li>
            @endforeach
        </ul>
    @endif
@endsection