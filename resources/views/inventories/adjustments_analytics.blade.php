@extends('supplier.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Adjustments Analytics</h5>
                    <div>
                        <a href="{{ route('inventories.adjustments') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Adjustments
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Date Range</h6>
                                <p class="mb-0">Showing analytics from <strong>{{ $startDate->format('M d, Y') }}</strong>
                                    to <strong>{{ $endDate->format('M d, Y') }}</strong></p>

                                <form action="{{ route('inventories.adjustments.analytics') }}" method="GET" class="mt-2">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <label for="date_from" class="col-form-label">From</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="date_from" name="date_from"
                                                class="form-control form-control-sm"
                                                value="{{ request('date_from', $startDate->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-auto">
                                            <label for="date_to" class="col-form-label">To</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="date" id="date_to" name="date_to"
                                                class="form-control form-control-sm"
                                                value="{{ request('date_to', $endDate->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Adjustment Trends Over Time</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="trendsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Adjustment Types Distribution</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="typesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Top Products with Adjustments</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Adjustments</th>
                                                <th>Increases</th>
                                                <th>Decreases</th>
                                                <th>Net Change</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topProducts as $productName => $data)
                                                <tr>
                                                    <td>{{ $productName }}</td>
                                                    <td>{{ $data['count'] }}</td>
                                                    <td class="text-success">+{{ $data['increases'] }}</td>
                                                    <td class="text-danger">-{{ $data['decreases'] }}</td>
                                                    <td class="{{ $data['net_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $data['net_change'] >= 0 ? '+' : '' }}{{ $data['net_change'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Top Users Making Adjustments</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="usersChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data for charts
                const dates = @json(array_keys($adjustmentsByDate->toArray()));
                const adjustmentCounts = dates.map(date => @json($adjustmentsByDate))[0].map(data => data.total);
                const increases = dates.map(date => @json($adjustmentsByDate))[0].map(data => data.increases);
                const decreases = dates.map(date => @json($adjustmentsByDate))[0].map(data => data.decreases);
                const damages = dates.map(date => @json($adjustmentsByDate))[0].map(data => data.damages);
                const expiries = dates.map(date => @json($adjustmentsByDate))[0].map(data => data.expiries);

                // Type distribution data
                const typeLabels = ['Increases', 'Decreases', 'Damages', 'Expiries', 'Corrections'];
                const typeData = [
                    @json($typeDistribution['increases']),
                    @json($typeDistribution['decreases']),
                    @json($typeDistribution['damages']),
                    @json($typeDistribution['expiries']),
                    @json($typeDistribution['corrections'])
                ];

                // Users data
                const userLabels = @json(array_keys($topUsers->toArray()));
                const userData = @json(array_values($topUsers->toArray()));

                // Trends chart
                const trendsCtx = document.getElementById('trendsChart').getContext('2d');
                new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: 'Total Adjustments',
                                data: adjustmentCounts,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                tension: 0.4
                            },
                            {
                                label: 'Increases',
                                data: increases,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                tension: 0.4
                            },
                            {
                                label: 'Decreases',
                                data: decreases,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Types chart
                const typesCtx = document.getElementById('typesChart').getContext('2d');
                new Chart(typesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: typeLabels,
                        datasets: [{
                            data: typeData,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(153, 102, 255, 0.7)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Users chart
                const usersCtx = document.getElementById('usersChart').getContext('2d');
                new Chart(usersCtx, {
                    type: 'bar',
                    data: {
                        labels: userLabels,
                        datasets: [{
                            label: 'Adjustments Made',
                            data: userData,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script>
    @endpush

@endsection