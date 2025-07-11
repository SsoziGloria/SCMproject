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
        <div class="card mx-auto mt-4" style="max-width: 800px;">
            <div class="card-body">
                <h5 class="card-title text-center" style="color: #4B49AC; font-weight: bold;">
                        📊 Customer Segments & Demand Forecast
                </h5>

                <!-- Responsive container inside card body -->
                <div style="width: 100%; max-width: 600px; margin: auto;">
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
                                    
                                    x: {
                                        type: 'category',
                                        title: { display: true, text: 'Customer ID  and  Prediction Date', color:'#4B49AC', font: { size: 16, weight: 'bold', family: 'Arial' } },
                                        ticks: {
                                            autoSkip: true,
                                            maxTicksLimit: 20,
                                        }
                                    },
                                    y1: {
                                        type: 'linear',
                                        display: true,
                                        position: 'left',
                                        title: { display: true, text: 'Total Quantity', color:'#4B49AC', font: { size: 14, weight: 'bold', family: 'Arial' } },
                                    },
                                    y2: {
                                        type: 'linear',
                                        display: true,
                                        position: 'right',
                                        title: { display: true, text: 'Predicted Quantity', color:'#4B49AC', font: { size: 14, weight: 'bold', family: 'Arial' } },
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
                    {{ $segments->links('pagination::bootstrap-5') }}

                </div>
            </div>
        </div>
        <!-- Cluster Descriptions -->
<div class="mt-3">
    <h6><strong>Cluster Descriptions and Insights from Customer Segments:</strong></h6>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>Cluster 0:</strong> Low spenders with infrequent purchases</li>
        <li class="list-group-item"><strong>Cluster 1:</strong> Medium spenders with moderate frequency</li>
        <li class="list-group-item"><strong>Cluster 2:</strong> High-value loyal customers with regular purchases</li>
        <li class="list-group-item"><strong>Cluster 3:</strong> New customers with uncertain behavior</li>
    </ul>
</div>
<br>

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