@extends('layouts.app') {{-- or your main layout --}}

@section('content')
<div class="container">
    <h1>Customer Segments</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Total Items Bought</th>
                <th>Recency (Days)</th>
                <th>Number of Orders</th>
                <th>Segment</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($segments as $segment)
                <tr>
                    <td>{{ $segment->customer_id }}</td>
                    <td>{{ $segment->total_items_bought }}</td>
                    <td>{{ $segment->recency_days }}</td>
                    <td>{{ $segment->num_orders }}</td>
                    <td>{{ $segment->segment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
