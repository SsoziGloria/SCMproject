@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Vendor Validation Result</h2>
    <div class="alert {{ $result === 'APPROVED' ? 'alert-success' : 'alert-danger' }}">
        <strong>{{ $result }}</strong>
    </div>
    <a href="{{ route('vendors.create') }}" class="btn btn-primary">Back to Form</a>
</div>
@endsection


