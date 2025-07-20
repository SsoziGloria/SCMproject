@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container">
        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/'dashboard.supplier'>Home</a></li>
                        <li class=" breadcrumb-item"><a href="/'dashboard.supplier'">cocoa suppliers</a></li>
                        <li class="breadcrumb-item active" aria-current="page"> Cocoa Suppliers</li>
                    </ol>
                </nav>
            </div>
        </div>

        <body>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">Uganda Cocoa Suppliers</h2>
                            <p class="text-muted mb-0">Chocolate and cocoa suppliers-Uganda</p>
                        </div>
                        <div>
                            <button class="btn btn-choco">Edit supplier information</button>
                            <button class="btn btn-outline-secondary">Settings</button>
                            <button class="btn btn-outline-danger">Change Status</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overview Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <button class="btn btn-outline-choco">Edit supplier information</button>
                                <button class="btn btn-outline-secondary">Settings</button>
                                <button class="btn btn-outline-danger">Change Status</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">About</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Uganda Cocoa Suppliers ltd is a certified chocolate supplier based in Kampala
                                They source cocoa beans from various regions, handle processing steps like fermentation,
                                drying, roasting and grinding and produce chocolate.
                                They offer a wide range of chocolate flavours including dark, milk and white.
                                They often provide technical assistance, research and development and customized solutions
                                to meet specific customer .

                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Details Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Supplier Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Company Name</dt>
                                        <dd class="col-sm-8"> Cocoa Suppliers ltd</dd>

                                        <dt class="col-sm-4">Contact Person</dt>
                                        <dd class="col-sm-8">Komuntale Paula</dd>

                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8">ChocolateSCM@supplier.com</dd>
                                        <dt class="col-sm-4">Rating</dt>
                                        <dd class="col-sm-8">4.8 / 5 ⭐️ (Based on 28 reviews)</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Telephone Number</dt>
                                        <dd class="col-sm-8">+ (256) 123-4567</dd>

                                        <dt class="col-sm-4">Address</dt>
                                        <dd class="col-sm-8">Plot 32 Industrial Area, Kampala, Uganda </dd>

                                        <dt class="col-sm-4">Tax ID</dt>
                                        <dd class="col-sm-8">TAX-9456456789</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </body>

    </html>
@endsection