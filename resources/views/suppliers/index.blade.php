@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <h1>Suppliers</h1>
    @if($suppliers->isEmpty())
        <p>No suppliers found.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection