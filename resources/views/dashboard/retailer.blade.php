@extends('retailer.app')

@section('content')
    <div class="pagetitle">
        <h1>Retailer Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Inventory Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card inventory-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Inventory</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $inventoryCount ?? 0 }}</h6>
                                        <span class="text-muted small pt-2 ps-1">items in stock</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Inventory Card -->

                    <!-- Low Stock Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card low-stock-card">
                            <div class="card-body">
                                <h5 class="card-title">Low Stock</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $lowStock->count() ?? 0 }}</h6>
                                        <span class="text-muted small pt-2 ps-1">items low</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Low Stock Card -->

                    <!-- Expiring Soon Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card expired-card">
                            <div class="card-body">
                                <h5 class="card-title">Expiring Soon</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $expiringSoon->count() ?? 0 }}</h6>
                                        <span class="text-muted small pt-2 ps-1">items</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Expiring Soon Card -->

                    <!-- Shipments Card (example) -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card shipments-card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Shipments</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $pendingShipments->count() ?? 0 }}</h6>
                                        <span class="text-muted small pt-2 ps-1">to deliver</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Shipments Card -->

                    <!-- Add more supplier-specific cards as needed -->
                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">
                <!-- Recent Activity (e.g., recent inventory changes) -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Inventory Activity</h5>
                        <div class="activity">
                            @foreach($recentActivity as $activity)
                                <div class="activity-item d-flex">
                                    <div class="activite-label">{{ $activity->time_ago }}</div>
                                    <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                    <div class="activity-content">
                                        {{ $activity->description }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div><!-- End Recent Activity -->

                <!-- News & Updates (optional) -->
                <div class="card">
                    <div class="card-body pb-0">
                        <h5 class="card-title">News &amp; Updates</h5>
                        <div class="news">
                            <!-- Supplier-related news here -->
                        </div>
                    </div>
                </div><!-- End News & Updates -->
            </div><!-- End Right side columns -->
        </div>
    </section>


    <!-- ML Combined Chart Section -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Customer Segments & Demand Forecast</h5>
        <canvas id="combinedChart" width="800" height="400"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Customer Segment Data
        const segmentLabels = @json($segments->pluck('customer_id'));
        const segmentData = @json($segments->pluck('total_quantity'));

        // Demand Prediction Data
        const predictionLabels = @json($predictions->pluck('prediction_date'));
        const predictionData = @json($predictions->pluck('predicted_quantity'));

        const ctx = document.getElementById('combinedChart').getContext('2d');
        const combinedChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [...new Set([...segmentLabels, ...predictionLabels])],
                datasets: [
                    {
                        label: 'Total Quantity (Customer Segments)',
                        data: segmentData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1',
                    },
                    {
                        label: 'Predicted Quantity (Demand Forecast)',
                        data: predictionData,
                        type: 'line',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4,
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
<!-- End ML Combined Chart Section -->

<!-- Customer Segments Table -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Customer Segments Table</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
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
            <table class="table table-bordered">
                <thead>
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

@endsection