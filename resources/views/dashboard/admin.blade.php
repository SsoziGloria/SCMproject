
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Labels and data from backend
        const segmentLabels = @json($segments->pluck('customer_id'));
        const segmentData = @json($segments->pluck('total_quantity'));

        const predictionLabels = @json($predictions->pluck('prediction_date'));
        const predictionData = @json($predictions->pluck('predicted_quantity'));

        const ctx = document.getElementById('combinedChart').getContext('2d');
        const combinedChart = new Chart(ctx, {
            type: 'line', 
            data: {
                // Use union of labels (dates + customer IDs combined as strings)
                labels: [...new Set([...segmentLabels, ...predictionLabels])],
                datasets: [
                    {
                        label: 'Total Quantity (Customer Segments)',
                        data: segmentData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y1',
                    },
                    {
                        label: 'Predicted Quantity (Demand Forecast)',
                        data: predictionData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y2',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Total Quantity' },
                    },
                    y2: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Predicted Quantity' },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
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