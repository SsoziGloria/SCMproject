@extends('layouts.app')

@section('content')
    <h2 style="text-align: center; margin-bottom: 20px; font-weight: bold;">Current Stock</h2>
    <a href="{{ route('inventories.create') }}" class="btn btn-primary mb-3">Add Inventory Record</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Expiration Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inventory)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $inventory->product_id }}</td>
                    <td>{{ $inventory->product_name }}</td>
                    <td>{{ $inventory->quantity }}</td>
                    <td>{{ $inventory->location }}</td>
                    <td>{{ $inventory->expiration_date }}</td>
                    <td>
                    <a href="{{ route('inventories.show', $inventory->id) }}" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No inventory records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection