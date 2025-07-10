@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Validate Vendor via Java API</h2>

    @if(session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif

    <form action="{{ route('vendors.validate') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="vendor_pdf" class="form-label">Upload Vendor PDF:</label>
            <input type="file" name="vendor_pdf" id="vendor_pdf" class="form-control" accept="application/pdf" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit for Validation</button>
    </form>
</div>
@endsection
