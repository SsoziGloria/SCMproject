@extends('admin.app')

@section('content')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-info fw-bold">User Analytics</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('analytics') }}">Analytics</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Analytics
            </a>
            <a href="{{ route('analytics.revenue') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> Revenue Analytics
            </a>
            <a href="{{ route('analytics.products') }}" class="btn btn-outline-success">
                <i class="bi bi-box-seam"></i> Product Analytics
            </a>
        </div>
    </div><!-- End Page Title -->

    <section class="section user-analytics">

        <!-- User Summary Cards -->
        <div class="row mb-4">
            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Total Users</h6>
                                <h3 class="fw-bold text-primary mb-2">{{ number_format($analytics['total_users']) }}</h3>
                                <small class="text-muted">All registered users</small>
                            </div>
                            <div class="card-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-people text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Active Users</h6>
                                <h3 class="fw-bold text-success mb-2">{{ number_format($analytics['active_users']) }}</h3>
                                <small class="text-muted">Last 30 days</small>
                            </div>
                            <div class="card-icon bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-person-check text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">New This Month</h6>
                                <h3 class="fw-bold text-info mb-2">{{ number_format($analytics['new_users_this_month']) }}</h3>
                                <small class="text-muted">New registrations</small>
                            </div>
                            <div class="card-icon bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-person-plus text-info fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">User Growth</h6>
                                <h3 class="fw-bold text-{{ $analytics['users_growth'] >= 0 ? 'success' : 'danger' }} mb-2">
                                    {{ $analytics['users_growth'] > 0 ? '+' : '' }}{{ $analytics['users_growth'] }}%
                                </h3>
                                <small class="text-muted">vs last month</small>
                            </div>
                            <div class="card-icon bg-{{ $analytics['users_growth'] >= 0 ? 'success' : 'danger' }} bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-{{ $analytics['users_growth'] >= 0 ? 'trending-up' : 'trending-down' }} text-{{ $analytics['users_growth'] >= 0 ? 'success' : 'danger' }} fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Average Customer Lifetime Value: UGX {{ number_format($analytics['customer_lifetime_value']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Charts -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>User Registration Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="userGrowthChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>User Roles</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="userRoleChart" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Activity Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Customer Activity (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="customerActivityChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Tables -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Customers</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Customer</th>
                                        <th>Orders</th>
                                        <th>Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['top_customers'] as $customer)
                                        <tr>
                                            <td class="ps-3">
                                                <div>
                                                    <strong class="text-primary">{{ $customer->name }}</strong>
                                                    <br><small class="text-muted">{{ $customer->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $customer->total_orders }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">UGX {{ number_format($customer->total_spent) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No customer data available</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>User Role Distribution</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3">Role</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                        <th>Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['user_roles'] as $role)
                                        <tr>
                                            <td class="ps-3">
                                                <i class="bi bi-person-{{ $role->role === 'admin' ? 'gear' : ($role->role === 'manager' ? 'check-square' : 'circle') }} me-2 text-muted"></i>
                                                <strong>{{ ucfirst($role->role) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $role->count }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: {{ $role->percentage }}%" 
                                                         aria-valuenow="{{ $role->percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ number_format($role->percentage, 1) }}%</small>
                                            </td>
                                            <td>
                                                @if(isset($role->growth) && $role->growth > 0)
                                                    <span class="text-success">
                                                        <i class="bi bi-arrow-up"></i> {{ $role->growth }}%
                                                    </span>
                                                @elseif(isset($role->growth) && $role->growth < 0)
                                                    <span class="text-danger">
                                                        <i class="bi bi-arrow-down"></i> {{ abs($role->growth) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="bi bi-dash"></i> 0%
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No user role data</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Registration Trend Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Recent Registration Activity</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>New Registrations</th>
                                        <th>Activity Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['user_registration_trend'] as $trend)
                                        <tr>
                                            <td class="ps-3">
                                                <strong>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $trend->count }}</span>
                                            </td>
                                            <td>
                                                @if($trend->count >= 10)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-arrow-up"></i> High
                                                    </span>
                                                @elseif($trend->count >= 5)
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-dash"></i> Medium
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-arrow-down"></i> Low
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <i class="bi bi-calendar3 text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No registration data available</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($analytics['user_growth_chart']['labels']),
                datasets: [{
                    label: 'New Users',
                    data: @json($analytics['user_growth_chart']['data']),
                    borderColor: 'rgb(23, 162, 184)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'New Registrations'
                        }
                    }
                }
            }
        });

        // User Role Chart
        const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
        new Chart(userRoleCtx, {
            type: 'doughnut',
            data: {
                labels: @json($analytics['user_role_chart']['labels']),
                datasets: [{
                    data: @json($analytics['user_role_chart']['data']),
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Customer Activity Chart
        const customerActivityCtx = document.getElementById('customerActivityChart').getContext('2d');
        new Chart(customerActivityCtx, {
            type: 'bar',
            data: {
                labels: @json($analytics['customer_activity_chart']['labels']),
                datasets: [{
                    label: 'Active Customers',
                    data: @json($analytics['customer_activity_chart']['data']),
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgb(255, 193, 7)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Active Customers'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>
@endpush
