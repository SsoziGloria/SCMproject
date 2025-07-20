@extends(auth()->user()->role . '.app')

@section('content')
    @include('dashboard')
@endsection