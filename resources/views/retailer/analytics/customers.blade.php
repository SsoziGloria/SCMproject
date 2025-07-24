@extends('retailer.app')

@section('content')
<div class="pagetitle">
    <h1>Customer Analytics</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retailer.analytics.index') }}">Analytics</a></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <!-- Period Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('retailer.analytics.customers') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="period" class="form-label">Time Period</label>
                            <select class="form-select" id="period" name="period" onchange="toggleCustomDates()">
                                <option value="7_days" {{ $selectedPeriod == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30_days" {{ $selectedPeriod == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90_days" {{ $selectedPeriod == '90_days' ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="custom" {{ $selectedPeriod == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="start_date_container" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }};">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3" id="end_date_container" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }};">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-people display-4" style="color: #d98323;"></i>
                    </div>
                    <h4>{{ number_format($customerData['total_customers'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">Total Customers</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-person-plus display-4 text-success"></i>
                    </div>
                    <h4>{{ number_format($customerData['new_customers'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">New Customers</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-arrow-repeat display-4 text-info"></i>
                    </div>
                    <h4>{{ number_format($customerData['returning_customers'] ?? 0) }}</h4>
                    <p class="text-muted mb-0">Returning Customers</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-star display-4 text-warning"></i>
                    </div>
                    <h4>{{ number_format(($customerData['returning_customers'] ?? 0) + ($customerData['new_customers'] ?? 0) > 0 ? (($customerData['returning_customers'] ?? 0) / (($customerData['returning_customers'] ?? 0) + ($customerData['new_customers'] ?? 0))) * 100 : 0, 1) }}%</h4>
                    <p class="text-muted mb-0">Retention Rate</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Customers -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Customers by Spending</h5>
                    @if(isset($customerData['customer_analysis']) && $customerData['customer_analysis']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Customer</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Avg Order</th>
                                    <th>Customer Since</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerData['customer_analysis'] as $index => $customer)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: #d98323;">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $customer['name'] }}</strong>
                                            <br><small class="text-muted">{{ $customer['email'] }}</small>
                                        </div>
                                    </td>
                                    <td>{{ number_format($customer['order_count']) }}</td>
                                    <td>UGX {{ number_format($customer['total_spent'], 0) }}</td>
                                    <td>UGX {{ number_format($customer['avg_order_value'], 0) }}</td>
                                    <td>
                                        @if($customer['first_order_date'])
                                            {{ \Carbon\Carbon::parse($customer['first_order_date'])->format('M Y') }}
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($customer['first_order_date'])->diffForHumans() }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($customer['total_spent'] >= 500000)
                                            <span class="badge bg-success">VIP</span>
                                        @elseif($customer['total_spent'] >= 100000)
                                            <span class="badge bg-warning">Premium</span>
                                        @else
                                            <span class="badge bg-secondary">Regular</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No customer data available for the selected period.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Segments -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Customer Segments</h5>
                    @if(isset($customerData['customer_segments']))
                    <div id="customerSegmentsChart"></div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="bi bi-circle-fill text-success"></i> High Value (UGX 500k+)</span>
                            <strong>{{ number_format($customerData['customer_segments']['high_value'] ?? 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="bi bi-circle-fill text-warning"></i> Medium Value (UGX 100k-500k)</span>
                            <strong>{{ number_format($customerData['customer_segments']['medium_value'] ?? 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-circle-fill text-secondary"></i> Low Value (< UGX 100k)</span>
                            <strong>{{ number_format($customerData['customer_segments']['low_value'] ?? 0) }}</strong>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No customer segment data available.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Insights -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Customer Insights & Recommendations</h5>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="insight-card p-3 border rounded mb-3">
                                <h6><i class="bi bi-lightbulb" style="color: #d98323;"></i> Growth Opportunity</h6>
                                <p class="text-muted small mb-0">
                                    @if(isset($customerData['customer_segments']['low_value']) && $customerData['customer_segments']['low_value'] > 0)
                                        You have {{ $customerData['customer_segments']['low_value'] }} low-value customers. 
                                        Consider targeted promotions to increase their order value.
                                    @else
                                        Your customer base shows strong engagement patterns.
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="insight-card p-3 border rounded mb-3">
                                <h6><i class="bi bi-trophy" style="color: #d98323;"></i> VIP Customers</h6>
                                <p class="text-muted small mb-0">
                                    @if(isset($customerData['customer_segments']['high_value']) && $customerData['customer_segments']['high_value'] > 0)
                                        {{ $customerData['customer_segments']['high_value'] }} VIP customers drive significant revenue. 
                                        Ensure excellent service to maintain loyalty.
                                    @else
                                        Focus on upgrading premium customers to VIP status.
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="insight-card p-3 border rounded mb-3">
                                <h6><i class="bi bi-people" style="color: #d98323;"></i> Customer Acquisition</h6>
                                <p class="text-muted small mb-0">
                                    @if(isset($customerData['new_customers']) && $customerData['new_customers'] > 0)
                                        {{ $customerData['new_customers'] }} new customers acquired. 
                                        Focus on retention strategies for sustainable growth.
                                    @else
                                        Consider marketing campaigns to attract new customers.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('retailer.reports.index') }}" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text"></i> Generate Customer Report
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> View Customer Orders
                        </a>
                        <a href="{{ route('retailer.analytics.sales') }}" class="btn btn-outline-primary">
                            <i class="bi bi-graph-up"></i> Sales Analytics
                        </a>
                        <a href="{{ route('retailer.analytics.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Analytics Overview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const startContainer = document.getElementById('start_date_container');
    const endContainer = document.getElementById('end_date_container');
    
    if (period === 'custom') {
        startContainer.style.display = 'block';
        endContainer.style.display = 'block';
    } else {
        startContainer.style.display = 'none';
        endContainer.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", () => {
    @if(isset($customerData['customer_segments']))
    // Customer Segments Chart
    const segments = @json($customerData['customer_segments']);
    const segmentLabels = ['High Value', 'Medium Value', 'Low Value'];
    const segmentData = [
        segments.high_value || 0,
        segments.medium_value || 0,
        segments.low_value || 0
    ];

    new ApexCharts(document.querySelector("#customerSegmentsChart"), {
        series: segmentData,
        chart: {
            height: 300,
            type: 'donut',
        },
        labels: segmentLabels,
        colors: ['#28a745', '#ffc107', '#6c757d'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 'bold'
                        },
                        value: {
                            show: true,
                            fontSize: '18px',
                            fontWeight: 'bold',
                            color: '#d98323'
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            color: '#666',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                }
            }
        }]
    }).render();
    @endif
});
</script>
@endpush
