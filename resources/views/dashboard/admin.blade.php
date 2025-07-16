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

                    <!-- Pending Orders Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card retailer-card">

                            <div class="card border-info shadow-sm">
                                <div class="card-body text-start">
                                    <h5 class="card-title">Pending Orders</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle bg-info text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                            style="width:40px; height:40px;">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 class="fw-bold">{{ $pendingOrders ?? 0 }}</h6>
                                            <a href="#" class="btn btn-sm btn-outline-info mt-2">View</a>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End PendingOrders Card -->


                        <!-- Returns Card -->
                        <div class="col-xxl-4 col-xl-12">

                            <div class="card info-card returns-card">

                                <div class="card-body">
                                    <h5 class="card-title">Returns </h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle bg-warning text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                            style="width:40px; height:40px;">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 class="fw-bold">{{ $returns ?? 0 }}</h6>
                                            <a href="#" class="btn btn-sm btn-outline-warning mt-2">View</a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- End Returns Card -->

                        <!-- delivered orders Card -->
                        <div class="col-xxl-4 col-xl-12">

                            <div class="card info-card supplierMessage-card">

                                <div class="card-body">
                                    <h5 class="card-title">Delivered Orders </h5>

                                    <div class="d-flex align-items-center">

                                        <div class="card-icon rounded-circle bg-success text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                            style="width:40px; height:40px;">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="ps-3">

                                            <h6 class="fw-bold">{{ $deliveredOrders ?? 0 }}</h6>
                                            <a href="#" class="btn btn-sm btn-outline-success mt-2">View</a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div><!-- End Right side columns -->

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">



            </div><!-- End Right side columns -->
        </div>

        <!-- ML Combined Chart Section -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0" style="min-height: 200px;">
                <div class="card-header bg-primary text-white text-center py-2">
                    <h5 class="mb-0">
                        📊 Customer Segments & Demand Forecast
                    </h5>
                </div>
                <div class="card-body p-3">
                    <!-- Responsive container inside card body -->
                    <div style="width: 100%; max-width: 1000px; margin: auto;">
                        <canvas id="combinedChart" height="600"></canvas>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Fetch Laravel data from Blade
                            const segmentLabels = @json($segments->pluck('customer_id')->map(fn($id) => 'Customer ' . $id));
                            const segmentData = @json($segments->pluck('total_quantity'));

                            const predictionLabels = @json($predictions->pluck('prediction_date'));
                            const predictionData = @json($predictions->pluck('predicted_quantity'));

                            // Build a unified label array for x-axis (for demonstration, we just concatenate for visual comparison)
                            const chartLabels = [...segmentLabels, ...predictionLabels];

                            // Align datasets to labels
                            const segmentDataPadded = [...segmentData, ...Array(predictionLabels.length).fill(null)];
                            const predictionDataPadded = [...Array(segmentLabels.length).fill(null), ...predictionData];

                            const ctx = document.getElementById('combinedChart').getContext('2d');

                            const combinedChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: chartLabels,
                                    datasets: [
                                        {
                                            label: 'Customer Segment Quantity',
                                            data: segmentDataPadded,
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            tension: 0.4,
                                            fill: false,
                                            borderWidth: 2,
                                            pointRadius: 3,
                                            pointHoverRadius: 5,
                                        },
                                        {
                                            label: 'Demand Prediction Quantity',
                                            data: predictionDataPadded,
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                            tension: 0.4,
                                            fill: false,
                                            borderWidth: 2,
                                            pointRadius: 3,
                                            pointHoverRadius: 5,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            labels: {
                                                font: {
                                                    size: 13,
                                                    weight: 'bold'
                                                }
                                            }
                                        },
                                        title: {
                                            display: true,

                                            font: {
                                                size: 18,
                                                weight: 'bold'
                                            }
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                        }
                                    },
                                    interaction: {
                                        mode: 'nearest',
                                        axis: 'x',
                                        intersect: false
                                    },
                                    scales: {
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Customers & Forecast Dates',
                                                font: {
                                                    size: 14,
                                                    weight: 'bold'
                                                }
                                            },
                                            ticks: {
                                                autoSkip: true,
                                                maxTicksLimit: 20
                                            }
                                        },
                                        y: {
                                            title: {
                                                display: true,
                                                text: 'Quantity',
                                                font: {
                                                    size: 14,
                                                    weight: 'bold'
                                                }
                                            },
                                            beginAtZero: true
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
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-header bg-light text-dark text-center py-2">
                    <h6 class="mb-0">📋 Customer Segments Table</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive small" style="max-height: 300px; overflow-y: auto;">
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
                        {{ $segments->links('pagination::bootstrap-5') }}

                    </div>
                </div>
            </div>
            <!-- End Customer Segments Table -->

            <!-- Cluster Descriptions -->
            @if($clusterSummaries->isNotEmpty())
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-secondary text-white text-center py-2">
                        <h6 class="mb-0">📊 Customer Cluster Profiles</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive small" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-hover text-center align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cluster</th>
                                        <th>Customer Count</th>
                                        <th>Description</th>
                                        <th>Product Types</th>
                                        <th>Recommendation Strategy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clusterSummaries as $summary)
                                        <tr>
                                            <td><strong>Cluster {{ $summary->cluster }}</strong></td>
                                            <td>{{ $summary->customer_count }}</td>
                                            <td>{{ $summary->description }}</td>
                                            <td>{{ $summary->product_types }}</td>
                                            <td>{{ $summary->recommendation_strategy }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-muted">No cluster summary data available yet.</p>
            @endif


            <!-- Demand Predictions Table -->
            <div class="card shadow-sm border-0mt-4">
                <div class="card-header bg-light text-dark text-center py-2">
                    <h6 class="mb-0">📋 Demand Predictions Table</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive small" style="max-height: 300px; overflow-y: auto;">
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