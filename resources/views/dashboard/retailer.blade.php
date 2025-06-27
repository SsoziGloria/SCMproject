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
@endsection