
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Suppliers ({{ $supplierCount }})</h1>
        <ul>
            @foreach($suppliers as $supplier)
                <li>{{ $supplier->name }}</li>
            @endforeach
        </ul>
    </div>
@endsection