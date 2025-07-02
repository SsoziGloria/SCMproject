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
                                            <a href="{{ route('orders.pending') }}"
                                                class="btn btn-sm btn-outline-info mt-2">View</a>

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
                                            <a href="{{ route('orders.cancelled') }}"
                                                class="btn btn-sm btn-outline-warning mt-2">View</a>

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
                                            <a href="{{ route('orders.completed') }}"
                                                class="btn btn-sm btn-outline-success mt-2">View</a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div><!-- End Right side columns -->

                </div>
    </section>
@endsection