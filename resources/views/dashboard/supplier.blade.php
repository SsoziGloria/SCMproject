@extends('supplier.app')

@section('content')
    <div class="pagetitle">
        <h1>Supplier Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="container mt-4">

        <section class="section dashboard">
            <div class="row">
                <!-- Dashboard Summary Cards -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Total Inventory Card -->
                        <div class="col-xxl-3 col-md-6 mb-4">
                            <div class="card info-card total inventory-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Inventory</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $inventoryCount }}</h6>
                                            <span class="text-muted small pt-2 ps-1">Current inventory</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Low Stock Card -->
                        <div class="col-xxl-3 col-md-6 mb-4">
                            <div class="card info-card low stock-card">
                                <div class="card-body">
                                    <h5 class="card-title">Low Stock Items</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $lowStock->count() }}</h6>
                                            <span class="text-muted small pt-2 ps-1">Stock available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Suppliers Card -->
                        <div class="col-xxl-3 col-md-6 mb-4">
                            <div class="card info-card suppliers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Suppliers</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $supplierCount }}</h6>
                                            <span class="text-muted small pt-2 ps-1">Suppliers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Expiring Soon Card -->
                        <div class="col-xxl-3 col-md-6 mb-4">
                            <div class="card info-card expired goods-card">
                                <div class="card-body">
                                    <h5 class="card-title">Expiring Soon</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ isset($expiringSoon) ? $expiringSoon->count() : 0 }}</h6>
                                            <span class="text-muted small pt-2 ps-1">Expiring soon</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Dashboard Summary Cards -->
            </div>

            <!-- Detailed Lists -->
            <div class="row">
                <!-- Low Stock Items List -->
                @if($lowStock->isNotEmpty())
                    <div class="col-lg-6">
                        <div class="card border-warning mb-4 shadow-sm">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="bi bi-exclamation-triangle-fill"></i> Low Stock Items
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($lowStock as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $item->product->name }}</span>
                                            <span class="badge bg-warning text-dark">Qty: {{ $item->quantity }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Expiring Soon Items List -->
                @if(isset($expiringSoon) && $expiringSoon->isNotEmpty())
                    <div class="col-lg-6">
                        <div class="card border-danger shadow-sm mb-4">
                            <div class="card-header bg-danger text-white fw-bold">
                                <i class="bi bi-hourglass-split"></i> Expiring Soon
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($expiringSoon as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $item->product->name }}</span>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- End Detailed Lists -->

        </section>
    </div>
@endsection