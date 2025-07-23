@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1> 🛒 Retailer </h1>
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
                <!-- Pending Orders Card -->
                <div class="col-xxl-4 col-md-6">
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
                                    <a href="{{ route('orders.pending') }}"
                                        class="btn btn-sm btn-outline-info mt-2">View</a>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6>{{ $inventoryCount ?? 0 }}</h6>
                                <span class="text-muted small pt-2 ps-1">items in stock</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End PendingOrders Card -->

                <!-- Returns Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card returns-card">
                        <div class="card-body">
                            <h5 class="card-title">Returns</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-warning text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="fw-bold">{{ $returns ?? 0 }}</h6>
                                    <a href="{{ route('orders.cancelled') }}"
                                        class="btn btn-sm btn-outline-warning mt-2">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Returns Card -->

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

                <!-- Delivered Orders Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card supplierMessage-card">
                        <div class="card-body">
                            <h5 class="card-title">Delivered Orders</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-success text-white mb-2 d-inline-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="fw-bold">{{ $deliveredOrders ?? 0 }}</h6>
                                    <a href="{{ route('orders.completed') }}"
                                        class="btn btn-sm btn-outline-success mt-2">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Delivered Orders Card -->
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
                            <div class="activite-label">
                                {{ $activity->time_ago }}
                            </div>
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