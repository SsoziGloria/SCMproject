

@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>📦 Dashboard</h1>
        <br>
        <p>Total Inventory: {{ $inventoryCount }}</p>
        <p>Low Stock Items: {{ $lowStock->count() }}</p>
        <p>Expiring Soon: {{ $expiringSoon->count() }}</p>
        <p>Total Suppliers: {{ $supplierCount }}</p>

    <div class="container mt-4">

    {{-- Low Stock Items --}}

    @if($lowStock->isNotEmpty())

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
    @endif

    @if(isset($expiringSoon) && $expiringSoon->isNotEmpty())
    <div class="card border-danger shadow-sm mb-4">
        <div class="card-header bg-danger text-white fw-bold">
            <i class="bi bi-hourglass-split"></i> Expiring Soon
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach ($expiringSoon as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $item->product->name }}</span>
                        <span class="badge bg-danger">{{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif


</div>



        
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

                    <!-- Sales Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Sales <span>| Today</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>0shs</h6>
                                        <span class="text-success small pt-1 fw-bold">0%</span> <span
                                            class="text-muted small pt-2 ps-1">no sales</span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Sales Card -->

                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Revenue <span>| This Month</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>0shs</h6>
                                        <span class="text-success small pt-1 fw-bold">0%</span> <span
                                            class="text-muted small pt-2 ps-1">no revenue</span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Revenue Card -->

                    <!-- Customers Card -->
                    <div class="col-xxl-4 col-xl-12">

                        <div class="card info-card customers-card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Customers <span>| This Year</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6></h6>
                                        <span class="text-danger small pt-1 fw-bold">0%</span> <span
                                            class="text-muted small pt-2 ps-1">no customer</span>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div><!-- End Customers Card -->

                    <!-- Reports -->
                    <div class="col-12">
                        <div class="card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Reports <span>/Today</span></h5>

                                <!-- Line Chart -->
                                <div id="reportsChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [{
                                                name: 'Sales',
                                                data: [0]
                                            }, {
                                                name: 'Revenue',
                                                data:[0] 
                                            }, {
                                                name: 'Customers',
                                                data:[0] 
                                            }],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy HH:mm'
                                                },
                                            }
                                        }).render();
                                    });
                                </script>
                                <!-- End Line Chart -->

                            </div>

                        </div>
                    </div><!-- End Reports -->

                    <!-- Recent Sales -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                                <table class="table table-borderless datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"><a href="#"></a></th>
                                            <td><!--name--></td>
                                            <td><a href="#" class="text-primary"><!-- chocolate product--></a></td>
                                            <td></td>
                                            <td><span class="badge bg-success"></span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"></a></th>
                                            <td><!--customer name--></td>
                                            <td><a href="#" class="text-primary"><!--chocolate product--></a>
                                            </td>
                                            <td><!--price--></td>
                                            <td><span class="badge bg-warning"></span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"></a></th>
                                            <td></td>
                                            <td><a href="#" class="text-primary"> </a></td>
                                            <td></td>
                                            <td><span class="badge bg-success"></span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"></a></th>
                                            <td></td>
                                            <td><a href="#" class="text-primar"></a></td>
                                            <td></td>
                                            <td><span class="badge bg-danger"></span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"></a></th>
                                            <td></td>
                                            <td><a href="#" class="text-primary"></a></td>
                                            <td></td>
                                            <td><span class="badge bg-success"></span></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Recent Sales -->

                    <!-- Top Selling -->
                    <div class="col-12">
                        <div class="card top-selling overflow-auto">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body pb-0">
                                <h5 class="card-title">Top Selling <span>| Today</span></h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">Preview</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Sold</th>
                                            <th scope="col">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="""></a>
                                            </th>
                                            <td><a href="#" class="text-primary fw-bold">
                                                    </a></td>
                                            <td></td>
                                            <td class="fw-bold"></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="" alt=""></a>
                                            </th>
                                            <td><a href="#" class="text-primary fw-bold">
                                                    </a></td>
                                            <td></td>
                                            <td class="fw-bold"></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="" alt=""></a>
                                            </th>
                                            <td><a href="#" class="text-primary fw-bold">
                        </a></td>
                                            <td></td>
                                            <td class="fw-bold"></td>
                                            <td></td>
                                        </tr>
                                        
                                        <tr>
                                            <th scope="row"><a href="#"><img src="" alt=""></a>
                                            </th>
                                            <td><a href="#" class="text-primary fw-bold">
                                                    </a></td>
                                            <td></td>
                                            <td class="fw-bold"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Top Selling -->

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

                <!-- Recent Activity -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Recent Activity <span>| Today</span></h5>

                        <div class="activity">

                            <div class="activity-item d-flex">
                                <div class="activite-label">32 min</div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content">
                                Not yet <a href="#" class="fw-bold text-dark">!launching soon</a> 
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">56 min</div>
                                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                                <div class="activity-content">
                                    <!--content here-->
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">2 hrs</div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                    <!-- content here-->
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">1 day</div>
                                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                <div class="activity-content">
                                    <a href="#" class="fw-bold text-dark"><!--data here--></a>
                                    
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">2 days</div>
                                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                <div class="activity-content">
                                    <!--content here-->
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">4 weeks</div>
                                <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                                <div class="activity-content">
                                    <!--content here-->
                                </div>
                            </div><!-- End activity item-->

                        </div>

                    </div>
                </div><!-- End Recent Activity -->

                <!-- Budget Report -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Budget Report <span>| This Month</span></h5>

                        <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                                    legend: {
                                        data: ['Allocated Budget', 'Actual Spending']
                                    },
                                    radar: {
                                        // shape: 'circle',
                                        indicator: [{
                                            name: 'Sales',
                                            max: 6500
                                        },
                                        {
                                            name: 'Administration',
                                            max: 16000
                                        },
                                        {
                                            name: 'Information Technology',
                                            max: 30000
                                        },
                                        {
                                            name: 'Customer Support',
                                            max: 38000
                                        },
                                        {
                                            name: 'Development',
                                            max: 52000
                                        },
                                        {
                                            name: 'Marketing',
                                            max: 25000
                                        }
                                        ]
                                    },
                                    series: [{
                                        name: 'Budget vs spending',
                                        type: 'radar',
                                        data: [{
                                            value: [4200, 3000, 20000, 35000, 50000, 18000],
                                            name: 'Allocated Budget'
                                        },
                                        {
                                            value: [5000, 14000, 28000, 26000, 42000, 21000],
                                            name: 'Actual Spending'
                                        }
                                        ]
                                    }]
                                });
                            });
                        </script>

                    </div>
                </div><!-- End Budget Report -->

        

        
            </div><!-- End Right side columns -->

        </div>
    </section>


@endsection