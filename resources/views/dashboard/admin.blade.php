{{-- Debug --}}
@isset($segments)
    <p>✅ Segments loaded: {{ count($segments) }}</p>
@else
    <p>❌ $segments is not set</p>
@endisset



@extends('admin.app')

@section('content')
    <div class="pagetitle">
        <h1>Admin Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                        <!-- ML Combined Chart Section -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Customer Segments & Demand Forecast</h5>
        <!-- Responsive container inside card body -->
        <div style="width: 100%; max-width: 900px; margin: auto;">
            <canvas id="combinedChart" height="400"></canvas>
        </div>

        <script>
    document.addEventListener("DOMContentLoaded", function () {
        const segmentLabels = [1, 2, 3];
        const segmentData = [100, 200, 300];
        const predictionLabels = ["2025-07-01", "2025-07-02", "2025-07-03"];
        const predictionData = [120, 250, 280];

        console.log("Chart loaded OK");

        const ctx = document.getElementById('combinedChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: predictionLabels,
                datasets: [
                    {
                        label: 'Actual',
                        data: segmentData,
                        borderColor: 'blue',
                        fill: false
                    },
                    {
                        label: 'Predicted',
                        data: predictionData,
                        borderColor: 'red',
                        fill: false
                    }
                ]
            }
        });
    });
</script>

    </div>
</div>
<!-- End ML Combined Chart Section -->

<!-- Customer Segments Table -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Customer Segments Table</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Customer ID</th>
                        <th>Quantity</th>
                        <th>Total Quantity</th>
                        <th>Purchase Count</th>
                        <th>Cluster</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($segments as $seg)
                        <tr>
                            <td>{{ $seg->customer_id }}</td>
                            <td>{{ $seg->quantity }}</td>
                            <td>{{ $seg->total_quantity }}</td>
                            <td>{{ $seg->purchase_count }}</td>
                            <td>{{ $seg->cluster }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- End Customer Segments Table -->

<!-- Demand Predictions Table -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Demand Predictions Table</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Product ID</th>
                        <th>Prediction Date</th>
                        <th>Predicted Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($predictions as $pred)
                        <tr>
                            <td>{{ $pred->product_id }}</td>
                            <td>{{ $pred->prediction_date }}</td>
                            <td>{{ $pred->predicted_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- End Demand Predictions Table -->
        </div>
    </section>


@endsection