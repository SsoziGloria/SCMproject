@extends('user.app')

@section('content')
    <div class="pagetitle">
        <h1>Welcome Back!</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.user') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <!-- Welcome Message -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-1">Good morning, {{ Auth::user()->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your chocolate orders today.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> New Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">My Orders</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart-check text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-primary">{{ Auth::user()->orders()->count() }}</h6>
                                <span class="text-muted small">Active orders</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">In Transit</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-truck text-warning"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-warning">3</h6>
                                <span class="text-muted small">Shipments on the way</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Delivered</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-success">8</h6>
                                <span class="text-muted small">This month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- My Recent Orders -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">My Recent Orders</h5>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                View All Orders
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Expected Delivery</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#ORD-2457</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/images/dark-chocolate.jpg" alt="Dark Chocolate"
                                                    class="rounded me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-bold">Dark Chocolate 70%</div>
                                                    <small class="text-muted">Premium Quality</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>500 kg</td>
                                        <td><span class="badge bg-info">In Transit</span></td>
                                        <td>Dec 15, 2024</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                Track
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#ORD-2456</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/images/milk-chocolate.jpg" alt="Milk Chocolate"
                                                    class="rounded me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-bold">Milk Chocolate</div>
                                                    <small class="text-muted">Premium Quality</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>300 kg</td>
                                        <td><span class="badge bg-warning">Processing</span></td>
                                        <td>Dec 18, 2024</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                Track
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#ORD-2455</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/images/cocoa-beans.jpg" alt="Cocoa Beans" class="rounded me-2"
                                                    width="32" height="32">
                                                <div>
                                                    <div class="fw-bold">Raw Cocoa Beans</div>
                                                    <small class="text-muted">Organic Certified</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>1000 kg</td>
                                        <td><span class="badge bg-success">Delivered</span></td>
                                        <td>Dec 10, 2024</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-success">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Tracking -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Track Your Shipment</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trackingNumber" class="form-label">Enter Tracking Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="trackingNumber"
                                            placeholder="e.g., TRK-2024-001">
                                        <button class="btn btn-primary" type="button">Track</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Need help?</strong> Contact our support team for assistance with tracking your
                                    orders.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Place New Order
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul me-2"></i>View All Orders
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="bi bi-grid-3x3-gap me-2"></i>Browse Catalog
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="bi bi-headset me-2"></i>Get Support
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Updates -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Updates</h5>
                        <div class="activity">
                            <div class="activity-item d-flex">
                                <div class="activite-label">2 hrs</div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content">
                                    Your order <strong>#ORD-2457</strong> has been shipped and is on its way!
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activite-label">1 day</div>
                                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                <div class="activity-content">
                                    Order <strong>#ORD-2456</strong> is being processed at our facility
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activite-label">2 days</div>
                                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                <div class="activity-content">
                                    New premium chocolate varieties now available in our catalog
                                </div>
                            </div>
                            <div class="activity-item d-flex">
                                <div class="activite-label">1 week</div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                    Thank you for your feedback on order <strong>#ORD-2450</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Contact -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Need Help?</h5>
                        <p class="text-muted">Our customer service team is here to help you with any questions about your
                            orders.</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <i class="bi bi-telephone text-primary fs-4"></i>
                                    <h6 class="mt-2">Call Us</h6>
                                    <p class="text-muted small">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-envelope text-primary fs-4"></i>
                                <h6 class="mt-2">Email Us</h6>
                                <p class="text-muted small">support@chocolatechain.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection