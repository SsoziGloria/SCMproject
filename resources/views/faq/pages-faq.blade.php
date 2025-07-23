<!DOCTYPE html>
<html lang="en">
@php $user = auth()->user(); @endphp

@if ($user->role === 'user')
@include('user.head')

<body>
    @include('user.header')
    @elseif ($user->role === 'supplier')
    @include('layouts.head')

    <body>
        @include('layouts.header')
        @include('supplier.aside')
        @elseif ($user->role === 'retailer')
        @include('layouts.head')

        <body>
            @include('layouts.header')
            @include('retailer.aside')
            @elseif ($user->role === 'admin')
            @include('layouts.head')

            <body>
                @include('layouts.header')
                @include('admin.aside')
                @else
                @include('layouts.head')

                <body>
                    @include('layouts.header')
                    @include('layouts.aside')
                    @endif


                    <main class="main" id="main">
                        <div class="pagetitle">
                            <h1>Frequently Asked Questions</h1>
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">Frequently Asked Questions</li>
                                </ol>
                            </nav>
                        </div><!-- End Page Title -->

                        <section class="section faq">
                            <div class="row">
                                <div class="col-lg-6">

                                    <div class="card basic">
                                        <div class="card-body">
                                            <h5 class="card-title">Basic Questions</h5>

                                            <div>
                                                <h6>1. What is a Supply Chain Management (SCM) system?</h6>
                                                <p>A Supply Chain Management system is a platform that helps track and
                                                    manage the
                                                    movement of products
                                                    from raw material sourcing to final delivery to customers. It
                                                    improves visibility,
                                                    coordination, and
                                                    efficiency.</p>
                                            </div>

                                            <div class="pt-2">
                                                <h6>2. How does this SCM system support the chocolate supply chain?</h6>
                                                <p>This system supports every stage of the chocolate supply chain—from
                                                    sourcing cocoa
                                                    beans, processing
                                                    and packaging, to wholesale distribution and retail—by providing
                                                    tools for inventory
                                                    management, order
                                                    tracking, and analytics.</p>
                                            </div>

                                            <div class="pt-2">
                                                <h6>3. What features are available for different users?</h6>
                                                <p>Each user role—such as supplier, manufacturer, wholesaler, and
                                                    retailer—has access to
                                                    tailored
                                                    features like inventory management, workforce allocation, order
                                                    processing,
                                                    communication tools, and
                                                    automated reporting.</p>
                                            </div>

                                        </div>
                                    </div>
                                </div>




                                <!-- F.A.Q Group: Chocolate Logistics -->
                                <div class="col-lg-6">
                                    <!-- F.A.Q Group 1 -->
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Chocolate Supply Chain Questions</h5>

                                            <div class="accordion accordion-flush" id="faq-group-1">

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsOne-1" type="button"
                                                            data-bs-toggle="collapse">
                                                            What challenges does the chocolate supply chain face?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsOne-1" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-1">
                                                        <div class="accordion-body">
                                                            Common challenges include unpredictable demand, seasonal
                                                            sourcing of cocoa,
                                                            complex logistics, and
                                                            the need for traceability across multiple stakeholders.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsOne-2" type="button"
                                                            data-bs-toggle="collapse">
                                                            How does machine learning help in this SCM system?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsOne-2" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-1">
                                                        <div class="accordion-body">
                                                            Machine learning is used to forecast product demand and to
                                                            segment customers
                                                            based on their
                                                            purchasing habits, allowing for better planning and
                                                            personalized marketing.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsOne-3" type="button"
                                                            data-bs-toggle="collapse">
                                                            Can this system validate new vendors?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsOne-3" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-1">
                                                        <div class="accordion-body">
                                                            Yes. A Java-based module checks submitted vendor
                                                            applications (in PDF
                                                            format) for credibility,
                                                            such as financial stability and compliance, and either
                                                            approves them or
                                                            schedules follow-up
                                                            inspections.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsOne-4" type="button"
                                                            data-bs-toggle="collapse">
                                                            What reports can stakeholders expect?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsOne-4" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-1">
                                                        <div class="accordion-body">
                                                            Stakeholders receive scheduled reports summarizing inventory
                                                            levels, sales
                                                            trends, workforce
                                                            distribution, and more—tailored to their role in the supply
                                                            chain.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsOne-5" type="button"
                                                            data-bs-toggle="collapse">
                                                            Is real-time communication supported?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsOne-5" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-1">
                                                        <div class="accordion-body">
                                                            Yes. The system includes a chat feature that enables
                                                            real-time communication
                                                            between suppliers,
                                                            manufacturers, and retailers to ensure smooth coordination.
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div><!-- End F.A.Q Group 1 -->

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Chocolate Logistics & Storage</h5>

                                            <div class="accordion accordion-flush" id="faq-group-logistics">

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsLogistics-1" type="button"
                                                            data-bs-toggle="collapse">
                                                            How is chocolate stored during transportation?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsLogistics-1" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-logistics">
                                                        <div class="accordion-body">
                                                            Chocolate requires cool, dry conditions and is typically
                                                            transported in
                                                            climate-controlled containers to prevent melting and
                                                            preserve quality.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsLogistics-2" type="button"
                                                            data-bs-toggle="collapse">
                                                            What are the key logistics stages in chocolate delivery?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsLogistics-2" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-logistics">
                                                        <div class="accordion-body">
                                                            The logistics stages include raw cocoa transport from farms,
                                                            delivery to
                                                            processing facilities, packaging, warehousing, distribution
                                                            to retailers,
                                                            and
                                                            shelf delivery.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed"
                                                            data-bs-target="#faqsLogistics-3" type="button"
                                                            data-bs-toggle="collapse">
                                                            How does the SCM system monitor chocolate conditions?
                                                        </button>
                                                    </h2>
                                                    <div id="faqsLogistics-3" class="accordion-collapse collapse"
                                                        data-bs-parent="#faq-group-logistics">
                                                        <div class="accordion-body">
                                                            The system integrates inventory tracking with logs for
                                                            temperature and
                                                            humidity,
                                                            enabling alerts when conditions are outside safe ranges
                                                            during transport and
                                                            storage.
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- End F.A.Q Group: Chocolate Logistics -->
                                </div>

                            </div>
                        </section>


                    </main>
                    @include('layouts.footer')

                    @include('layouts.scripts')
                </body>

</html>